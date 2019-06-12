<?php
namespace Hub\Blade;

use Hub\Blade\BladeOne;

class Renderer extends \Hub\Base\Base
{
    public static function output($fp, $data)
    {
        $views = dirname(dirname(__DIR__));
        $cache = dirname(dirname(__DIR__)) . '/cache';
        $blade = new BladeOne($views,$cache,BladeOne::MODE_AUTO);
        return $blade->run($fp,$data);
    }
}
