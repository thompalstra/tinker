<?php
namespace Hub\Queue;

use Exception;

use Thread;

use Frame;

use Hub\Base\Base;

class Worker extends Thread
{

    public $completed = false;
    public $failed = false;
    public $done = false;

    public function __construct($storage)
    {
        $this->storage = $storage;
    }

    public function complete()
    {
        $this->completed = true;
        $this->failed = false;
    }

    public function fail()
    {
        $this->completed = false;
        $this->failed = true;
    }

    public function done()
    {
        $this->done = true;
    }

    public function isCompleted()
    {
        return $this->completed;
    }

    public function isFailed()
    {
        return $this->failed;
    }

    public function isDone()
    {
        return $this->done;
    }

    public function run()
    {
        include(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php');
        $application = new \Hub\Application();
        $payload = json_decode($this->storage['payload'], true);
        try{
            preg_match('/(.*)@(.*)/', $this->job->task, $matches);
            if(count($matches) == 3){
                $this->storage['running'] = '1122';
                $class = $matches[1];
                $method = $matches[2];
                $instance = new $class();
                $instance->payload = $payload;
                $instance->$method();
                $payload = $instance->payload;
            }
            $this->complete();
        } catch(Exception $e){
            $this->message = $e->getMessage();
            $this->exception = $e->getTraceAsString();
            $this->fail();
        }
        $this->storage['payload'] = json_encode($payload);
        $this->done();
    }
}
