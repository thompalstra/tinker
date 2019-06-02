<?php
namespace migrations;

use Hub\Db\Migration;

class {class} extends Migration
{
    public function up()
    {
        if(Query::hasTable('queues') == false){
            Query::createTable('queues', [
                'id' => 'INT(11) PRIMARY KEY AUTO_INCREMENT',
                'queue' => 'VARCHAR(255) NOT NULL',
                'workers' => 'INT(1) NOT NULL',
                'updated_at' => 'INT(11)',
                'created_at' => 'INT(11)'
            ]);

            $queue = new \Hub\Queue\Queue();
            $queue->queue = 'default';
            $queue->workers = 3;
            $queue->created_at = time();
            $queue->updated_at = time();
            $queue->save();
        }

        if(Query::hasTable('jobs') == false){
            Query::createTable('jobs', [
                'id' => 'INT(11) PRIMARY KEY AUTO_INCREMENT',
                'queue' => 'VARCHAR(255) NOT NULL',
                'status' => 'INT(11) NOT NULL',
                'task' => 'VARCHAR(255) NOT NULL',
                'arguments' => 'TEXT NOT NULL',
                'updated_at' => 'INT(11)',
                'created_at' => 'INT(11)',
            ]);
        }

        if(Query::hasTable('failed_jobs') == false){
            Query::createTable('failed_jobs', [
                'id' => 'INT(11) PRIMARY KEY AUTO_INCREMENT',
                'queue' => 'VARCHAR(255) NOT NULL',
                'status' => 'INT(11) NOT NULL',
                'task' => 'VARCHAR(255) NOT NULL',
                'arguments' => 'TEXT NOT NULL',
                'message' => 'TEXT NOT NULL',
                'updated_at' => 'INT(11)',
                'created_at' => 'INT(11)',
            ]);
        }

        if(Query::hasTable('completed_jobs') == false){
            Query::createTable('completed_jobs', [
                'id' => 'INT(11) PRIMARY KEY AUTO_INCREMENT',
                'queue' => 'VARCHAR(255) NOT NULL',
                'status' => 'INT(11) NOT NULL',
                'task' => 'VARCHAR(255) NOT NULL',
                'arguments' => 'TEXT NOT NULL',
                'updated_at' => 'INT(11)',
                'created_at' => 'INT(11)',
            ]);
        }
    }

    public function down()
    {
        if(Query::hasTable('queues')){
            Query::dropTable('queues');
        }
        if(Query::hasTable('jobs')){
            Query::dropTable('jobs');
        }
        if(Query::hasTable('failed_jobs')){
            Query::dropTable('failed_jobs');
        }
        if(Query::hasTable('completed_jobs')){
            Query::dropTable('completed_jobs');
        }
    }
}
