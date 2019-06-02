<?php
namespace App\Http\Controllers;

use Frame;
use App\Job;

class QueueController extends \Hub\Base\Controller
{
    public function create(int $amount)
    {
        $x = 0;
        for ($i = 0; $i < $amount; $i++) {
            $job = new Job();
            $job->queue = "default";
            $job->task = "\App\Tasks\Base@execute";
            $job->arguments = json_encode([
                "sleep" => rand(100, 2000)
            ]);
            $result = $job->save();
            if ($result) {
                $x++;
            }
        }
        echo "Succesfully inserted {$x} records!<br/>";
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
