<?php
namespace Hub\Base;

use Exception;

class Base
{

    public function load($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
        return true;
    }

    public static function getClassName()
    {
        return get_called_class();
    }

    public function __get($key)
    {
        return $this->$key;
    }

    public function __set($key, $value)
    {
        $this->$key = $value;
    }

    public function __call($method, $arg)
    {
        if(strlen($method) > 3) {
            $attr = strtolower(preg_replace('/\B([A-Z])/', '_$1', substr($method, 3, strlen($method))));
            switch(substr($method, 0, 3)){
                case "get":
                    if(property_exists($this, $attr)){
                        return $this->$attr;
                    }
                    return null;
                break;
                case "set":
                    $this->$attr = $arg[0];
                break;
            }
        } else if(method_exists($this, $method)){
            call_user_func_array([$this, $method], $arg);
        } else {
            $class = self::class;
            throw new Exception("Undefined method {$class}->{$method}");
        }
    }

    public static function __callStatic($method, $arg)
    {
        $class = self::class;
        if(method_exists($class, $method)){
            call_user_func_array([$class, $method], $arg);
        } else {
            throw new Exception("Undefined method {$class}:{$method}", 500);
        }
    }
}
