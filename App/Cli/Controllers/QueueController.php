<?php
namespace App\Cli\Controllers;

use Frame;

use Hub\Db\Query;

use Hub\Queue\Manager;
use Hub\Queue\Queue;
use Hub\Queue\Worker;
use Hub\Queue\Job;

class QueueController extends \Hub\Base\Controller
{
    public function start(string $queue, int $async = 1)
    {
        Queue::start($queue, $async);
    }

    public function restart(string $queue, string $ids = "")
    {
        Queue::restart($queue, $ids);
    }

    public function random()
    {
        return [
            'class' => '\App\Task',
            'method' => 'execute',
            'parameters' => [ rand(0, 200) ]
        ];
    }
}
