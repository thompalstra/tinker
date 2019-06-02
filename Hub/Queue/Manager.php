<?php
namespace Hub\Queue;

use Hub\Base\Base;

class Manager extends Base
{
    protected $workers = [];
    protected $available_count = 1;

    protected $working = 0;

    protected $async = true;

    protected $completed = 0;
    protected $total = 0;

    protected $failed_count = 0;
    protected $completed_count = 0;
    protected $total_count = 0;

    public function __construct()
    {
    }

    public function addToQueue($data)
    {
        $this->queue->add($data);
    }

    public function count()
    {
        return count($this->workers);
    }

    public function dispatch($job)
    {
        $job->status = Job::STATUS_WORKING;
        $job->save();

        $this->working++;
        $storage = new \Threaded();
        $worker = new Worker($storage);
        $worker->job = $job;
        $storage['payload'] = $job->arguments;
        $this->workers[] = $worker;
        $worker->start();
    }

    public function complete(array $collection)
    {
        if (!empty($collection)) {
            $where = [];

            foreach ($collection as $index => $worker) {
                $job = $worker->job;
                $where[] = [$job->queue, $job->task, $job->arguments, time(), time()];
            }

            \Hub\Db\Query::insert(\Hub\Queue\CompletedJob::class, [
                "queue", "task", "arguments", "updated_at", "created_at"
            ], $where);
        }
    }

    public function fail(array $collection)
    {
        if (!empty($collection)) {
            $where = [];

            foreach ($collection as $index => $worker) {
                $job = $worker->job;
                $where[] = [$job->queue, $job->task, $job->arguments, $worker->message, $worker->exception, time(), time()];
            }

            \Hub\Db\Query::insert(\Hub\Queue\FailedJob::class, [
                "queue", "task", "arguments", "message", "exception", "updated_at", "created_at"
            ], $where);
        }
    }

    public function delete(array $ids)
    {
        if (!empty($ids)) {
            return \Hub\Db\Query::delete(\App\Job::class, [
                ["id", "IN", $ids]
            ]);
        }
    }

    public function logColumn($prepend = "", $text = "", $append = "", $length = 25)
    {
        $text = "{$prepend}{$text}";

        $text = str_pad($text, $length, " ");


        $textLength = strlen($text);
        $appendLength = strlen($append);


        if ($textLength >= $length) {
            $text = substr($text, 0, ($length - $appendLength));
        }



        return "{$text}{$append}";
    }

    public function log()
    {
        echo $this->logColumn("[", "Completed: {$this->completed_count}", "]", 25);
        echo $this->logColumn("[", "Failed: {$this->failed_count}", "]", 25);
        echo $this->logColumn("[", "Total: {$this->total_count}", "]", 25);
        echo "\r";
    }

    public function start($queue, $async)
    {
        $this->queue = Queue::findOne([
            ['queue', '=', $queue]
        ]);

        if($this->queue){
            $this->async = $async;
            $this->available_count = $this->queue->workers;
            $this->async = $async;
            $this->time = time();

            $this->log();

            while(true){
                $failed = [];
                $completed = [];
                $ids = [];

                $timediff = time() - $this->time;
                $days=intval($timediff/86400);
                $remain=$timediff%86400;
                $hours=intval($remain/3600);
                $remain=$remain%3600;
                $mins=intval($remain/60);
                $secs=$remain%60;

                if ($secs == 1) {
                    $this->log();
                    $this->total_count = $this->completed_count = $this->failed_count = 0;
                    $this->time = time();
                }

                foreach ($this->workers as $index => $worker) {
                    if ($worker->isDone()) {
                        if ($worker->isFailed()) {
                            $failed[$index] = $worker;
                            $ids[] = $worker->job->id;
                            $this->failed_count++;
                            $this->total_count++;
                        } else if ($worker->isCompleted()) {
                            $completed[$index] = $worker;
                            $ids[] = $worker->job->id;
                            $this->completed_count++;
                            $this->total_count++;
                        }
                        unset($this->workers[$index]);
                    }
                }

                $this->complete($completed);
                $this->fail($failed);
                $this->delete($ids);

                foreach ($this->queue->getNext($this->available_count - $this->count()) as $next) {
                    $this->dispatch($next);
                }

            }
        } else {
            echo "Missing record for queue '{$queue}'... aborting.\n"; exit;
        }
    }
}
