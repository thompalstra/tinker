<?php
namespace Hub\Queue;

use Hub\Base\Model;

class FailedJob extends Model
{
    const STATUS_PENDING = 0;
    const STATUS_WORKING = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_FAILED = 3;

    public static $columns = [
        'id' => 'PRIMARY_KEY AUTO_INCREMENT',
        'queue' => 'VARCHAR(255) NOT NULL',
        'task' => 'VARCHAR(255) NOT NULL',
        'arguments' => 'TEXT NOT NULL',
        'message' => 'TEXT NOT NULL',
        'exception' => 'TEXT NOT NULL',
        'updated_at' => 'INT(11)',
        'created_at' => 'INT(11)',
    ];
}
