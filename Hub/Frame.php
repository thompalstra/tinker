<?php

class Frame extends \Hub\Base\Base
{
    public static $app;

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
}
