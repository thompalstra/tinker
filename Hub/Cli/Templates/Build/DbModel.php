<?php
namespace {namespace};

use Hub\Base\Model;

class {class} extends Model
{
    public static $columns = [
        'id' => 'INT(11) PRIMARY KEY AUTO_INCREMENT',
        'created_at' => 'INT(11) NOT NULL',
        'updated_at' => 'INT(11) NOT NULL'
    ];

    public static function seed()
    {
        return [
            ["id", "created_at", "updated_at"],
            [
                [1, time(), time()],
                [2, time(), time()],
                [3, time(), time()]
            ]
        ];
    }
}
