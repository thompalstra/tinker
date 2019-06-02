<?php
namespace App;

use Hub\Base\Base;

class Job extends \Hub\Queue\Job
{
    public $status = self::STATUS_PENDING;
}
