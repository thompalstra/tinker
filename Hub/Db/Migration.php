<?php

namespace Hub\Db;

use Frame;

use Hub\Base\Model;

class Migration extends Model
{
    public static $table = 'migrations';
    public static $columns = [
        'id' => 'INT(11) PRIMARY KEY AUTO_INCREMENT',
        'name' => 'VARCHAR(255) NOT NULL',
        'created_at' => 'INT(11) NOT NULL',
        'updated_at' => 'INT(11) NOT NULL'
    ];

    public static function check()
    {
        if(Query::hasTable('migrations') == false){
            Query::createTable('migrations', self::$columns);
        }
    }

    public static function migrate($steps)
    {

        self::check();

        echo "\n";

        foreach(scandir('migrations') as $file){
            if(in_array($file, ['.', '..'])){
                continue;
            }
            preg_match('/(.*).php/', $file, $matches);

            if(count($matches) == 2){
                $name = $matches[1];
                $exists = self::find()->where([
                    ['name', '=', $name]
                ])->exists();

                if($exists == false){
                    require_once("migrations/{$file}");
                    echo "[{$name}] \033[1;33mMigrating...\033[0m\n";
                    $migration = new $name();
                    $migration->name = $name;
                    $migration->created_at = time();
                    $migration->updated_at = time();
                    $migration->up();
                    $migration->save();
                    echo "[{$migration->name}] \033[0;32mMigrated successfully!\033[0m\n";
                }
            }
        }
    }

    public static function rollback($steps)
    {
        $query = self::find()->order([
            'created_at desc'
        ]);
        if($steps > 0){
            $query->limit($steps);
        }

        $migrations = $query->all();

        foreach($migrations as $migration){
            echo "\n[{$migration->name}] \033[1;33mReverting...\033[0m\n";
            $file = "migrations/{$migration->name}.php";
            if(file_exists($file)){
                require_once($file);
                $name = $migration->name;
                $fMigration = new $name();
                $fMigration->down();
                $migration->delete();
                echo "[{$migration->name}] \033[0;32mReverted successfully\033[0m\n";
            } else {
                echo "Unable to revert migration {$migration->name}: File does not exist\n";
            }
        }
    }
}
