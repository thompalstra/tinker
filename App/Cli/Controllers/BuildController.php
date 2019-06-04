<?php
namespace App\Cli\Controllers;

use Frame;

class BuildController extends \Hub\Base\Controller
{
    public function model(string $name, int $usedb = 1)
    {
        $fp = $namespace =  ['App'];
        $class = $name;

        if(strpos($name, '\\') > -1){ // includes a path
            $path = explode('\\', $name);
            $class = array_pop($path);
            $namespace[] = implode('\\', $path);
            $fp[] = implode('\\', $path);
            $fp[] = $class;
        } else {
            $fp[] = $class;
        }

        $fp = Frame::path($fp);
        $namespace = Frame::ns($namespace);

        $loc = $usedb ? 'Hub\Cli\Templates\Build\DbModel.php' : 'Hub\Cli\Templates\Build\BaseModel.php';
        $template = file_get_contents($loc);
        $template = str_replace(['{namespace}', '{class}'], [$namespace, $class], $template);

        if(!is_dir(dirname($fp))){
            mkdir(dirname($fp), 0777, true);
        }

        file_put_contents("{$fp}.php", $template);
    }

    public function controller(string $name, int $cli = 0)
    {
        $fp = $namespace =  ['App', ($cli ? 'Cli' : 'Http'), 'Controllers'];
        $class = $name;

        if(strpos($name, '\\') > -1){ // includes a path
            $path = explode('\\', $name);
            $class = array_pop($path);
            $namespace[] = implode('\\', $path);
            $fp[] = implode('\\', $path);
            $fp[] = $class;
        } else {
            $fp[] = $class;
        }

        $fp = Frame::path($fp);
        $namespace = Frame::ns($namespace);

        $loc = 'Hub\Cli\Templates\Build\Controller.php';
        $template = file_get_contents($loc);
        $template = str_replace(['{namespace}', '{class}'], [$namespace, $class], $template);

        if(file_exists("{$fp}.php")){
            echo "File already exists: {$fp}.php\n";
            exit;
        }

        if(!is_dir(dirname($fp))){
            mkdir(dirname($fp), 0777, true);
        }

        file_put_contents("{$fp}.php", $template);
    }

    public function queue()
    {
        $loc = 'Hub\Cli\Templates\Build\queue.php';
        $time = time();
        $class = "m{$time}_queue";
        $fp = "migrations/{$class}";

        $template = file_get_contents($loc);
        $template = str_replace(['{class}'], [$class], $template);

        if(!is_dir(dirname($fp))){
            mkdir(dirname($fp), 0777, true);
        }

        file_put_contents("{$fp}.php", $template);
    }

    public function migration(string $name)
    {
        $loc = 'Hub\Cli\Templates\Build\Migration.php';
        $time = time();
        $class = "m{$time}_{$name}";
        $fp = "migrations/{$class}";

        $template = file_get_contents($loc);
        $template = str_replace(['{class}'], [$class], $template);

        if(!is_dir(dirname($fp))){
            mkdir(dirname($fp), 0777, true);
        }

        file_put_contents("{$fp}.php", $template);
    }
}
