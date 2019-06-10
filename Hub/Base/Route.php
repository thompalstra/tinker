<?php
namespace Hub\Base;

use Frame;

class Route extends Base
{

    protected static $names = [];
    protected static $routes = [];
    protected static $host;

    public function __construct($path, $parameters)
    {
        $this->setPath($path);
        $this->setParameters($parameters);
    }

    public static function register($method, $route, $controller, $options = [])
    {
        $routes = self::$routes;
        $routes[] = $route;
        $route = implode("/", $routes);

        if (!empty($options["name"])) {
            self::$names[] =  $options["name"];

            Frame::$app->routes["names"][implode(self::$names)] = "{$method}:{$route}";
        }

        Frame::$app->routes[self::$host][$method][$route] = $controller;
    }

    public static function get($route, $controller, $options = [])
    {
        self::register("get", $route, $controller, $options);
    }

    public static function post($route, $controller, $options = [])
    {
        self::register("post", $route, $controller, $options);
    }

    public static function put($route, $controller, $options = [])
    {
        self::register("put", $route, $controller, $options);
    }

    public static function delete($route, $controller, $options = [])
    {
        self::register("delete", $route, $controller, $options);
    }

    public static function matches($matches, $route, $controller, $options = [])
    {
        foreach ($matches as $match) {
            self::register($match, $route, $controller, $options);
        }
    }

    public static function group(array $options, $closure)
    {
        if(isset($options["name"]) && !empty($options["name"])){
            self::$names[] = $options["name"];
        }

        if(isset($options["route"]) && !empty($options["route"])){
            self::$routes[] = $options["route"];
        }

        if (isset($options["host"])) {
            self::$host = $options["host"];
        }

        $closure();

        if(isset($options["route"]) && !empty($options["route"])){
            array_pop(self::$routes);
        }

        if(isset($options["name"]) && !empty($options["name"])){
            array_pop(self::$names);
        }

        self::$host = "*";
    }
}
