<?php
namespace App\Tasks;

use Hub\Queue\Task;

class Base extends Task
{
    public function execute()
    {
        $sleep = rand(100, 4000) / 1000;

        usleep($this->payload['sleep'] * 1000);

        $chance = rand(1,100);
        if($chance > 90){
            throw new \Exception("This aint good bro : {$chance} but slept for {$sleep}!");
        }

        $this->payload['chance'] = $chance;
    }
}
