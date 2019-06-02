<?php
namespace Hub\Queue;

use Frame;

use Hub\Base\Model;

use Hub\Db\Query;

class Queue extends Model
{
    public static $columns = [
        'id' => 'PRIMARY_KEY AUTO_INCREMENT',
        'queue' => 'VARCHAR(255) NOT NULL',
        'workers' => 'INT(1) NOT NULL',
        'updated_at' => 'INT(11)',
        'created_at' => 'INT(11)',
    ];

    public static function start(string $queue, int $async = 0)
    {
        $manager = new Manager();
        Frame::$app->queues[$queue] = $manager;
        Frame::$app->queues[$queue]->start($queue, $async);
    }

    public static function restart(string $queue, string $ids = "")
    {
        $where = [];
        if (!empty($queue)) {
            $where[] = ["queue", "=", $queue];
        }

        if (!empty($ids)) {
            $where[] = ["id", "IN", explode(",", $ids)];
        }

        $result = Query::update(Job::class, [
            ["status", "=", 0]
        ], $where);

        if ($result) {
            if (!empty($ids)) {
                echo "Succesfully restarted ids: '{$ids}' for queue '{$queue}'";
            } else {
                echo "Successfully restarted queue '{$queue}'.\n";
            }
        }
    }

    public function getNext($limit)
    {
        return Job::find()
            ->where([
                ['queue', '=', $this->queue],
                ['status', '=', \App\Job::STATUS_PENDING]
            ])
            ->limit($limit)
            ->all();
    }
}
