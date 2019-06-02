<?php
namespace App\Cli\Controllers;

use Frame;
use ReflectionMethod;

class HomeController extends \Hub\Base\Controller
{
    public function index()
    {
        $out = [];
        echo str_pad("\nCommand", 31, ' ', STR_PAD_RIGHT) . "Arguments\n\n";
        foreach(Frame::$app->getRoutes()['get'] as $route => $controller){
            if(empty($route)){ continue; }

            $route = str_replace('/', ':', $route);
            preg_match('/(.*)@(.*)/', $controller, $matches);

            $controller =  $matches[1];
            $method = $matches[2];

            $controller = Frame::ns([Frame::$app->request->getControllerPath(), $controller]);

            if(class_exists($controller)){
                $lines = [];
                $command = str_pad($route, 30, ' ', STR_PAD_RIGHT);
                $arguments = [];
                if(method_exists($controller, $method)){
                    $reflectionMethod = new ReflectionMethod($controller, $method);
                    foreach($reflectionMethod->getParameters() as $param){
                        $name = $param->getName();
                        $required = $param->isOptional() ? "optional" : "required";
                        $type = $param->getType();
                        if($type){
                            $arguments[] = "--{$name}=({$type}:{$required})";
                        } else {
                            $arguments[] = "--{$name}=({$required})";
                        }
                    }
                }
                $arguments = implode(" ", $arguments);
            }
            echo "{$command}{$arguments}\n";
        }

        echo "\r\n";
    }
}
