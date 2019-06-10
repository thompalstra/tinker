<?php
namespace Hub\Base;

use ReflectionMethod;

use Frame;

class Controller extends Base
{
    protected $layout = "default/layouts/main";

    public function error($message)
    {
        http_response_code(404);
        echo "{$message}"; exit;
    }

    public function run($method, $arg)
    {
        if(method_exists($this, $method)){
            $params = $this->parameters($method, $arg);
            call_user_func_array([$this, $method], $params);
        } else {
            $class = get_called_class();
            return $this->run('error', ['message' => "{$class}::{$method} does not exist"]);
        }
    }

    public function parameters($method, $arg)
    {
        $reflectionMethod = new ReflectionMethod($this, $method);
        $params = [];
        foreach($reflectionMethod->getParameters() as $param){
            $paramType = null;
            if(method_exists($param, "getType")){
                $paramType = $param->getType();
            }
            $paramName = $param->getName();
            $isOptional = $param->isOptional();
            $paramTypeName = ($paramType) ? $paramType->getName() : null;
            $paramDefaultValue = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
            if(isset($arg[$paramName])){
                $params[] = $arg[$paramName];
            } elseif($isOptional && isset($paramDefaultValue)){
                $params[] = $paramDefaultValue;
            } else {
                return $this->run('error', ['message' => "Missing required parameters '{$paramName}'"]);
            }
        }
        return $params;
    }

    public function resolve($controller)
    {
        return Frame::ns([Frame::$app->request->getControllerPath(), $controller]);
    }

    public function redirect($url)
    {
        header("Location: {$url}"); exit;
    }
}
