<?php

class Frame
{
    public static $app;

    protected static $classmap = [];

    public static function path(array $params = [])
    {
        foreach($params as $k => $v){
            $params[$k] = str_replace(["\\", "/"], DIRECTORY_SEPARATOR, $v);
        }
        return implode(DIRECTORY_SEPARATOR, $params);
    }

    public static function ns(array $params = [])
    {
        foreach($params as $k => $v){
            $params[$k] = str_replace(["\\", "/"], "\\", $v);
        }
        return implode("\\", $params);
    }

    public static function root()
    {
        return self::$app->root;
    }

    public static function autoload($class)
    {
        if (empty(self::$classmap)) {
            self::$classmap = include("classmap.php");
        }

        if (isset(self::$classmap[$class])) {
            return require_once(self::$classmap[$class]);
        }
        return false;
    }
}
