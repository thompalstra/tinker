<?php
namespace Hub\Base;

use Frame;

class Request extends Base implements RequestInterface
{
    protected $path = "";
    protected $parameters = [];

    public function __construct(array $options = [])
    {
        $this->process($options);
    }

    public function process(array $options = [])
    {
        $class = self::class;
        trigger_error("Call to undefined method {$class}::process", E_USER_ERROR);
    }

    public function addParameter($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    public function getRoute()
    {
        foreach(Frame::$app->routes[$this->getMethod()] as $path => $controller){
            if($this->matchRoute($this->getPath(), $path)){
                return [Frame::path([$controller]), $this->getParameters()];
            }
        }
        return ["404", []];
    }

    public function matchRoute($source, $target)
    {
        $sourceParts = explode("/", $source);
        $targetParts = explode("/", $target);
        $i = 0;
        $params = [];

        if(count($sourceParts) == count($targetParts)){
            foreach($targetParts as $index => $targetPart){
                preg_match('/{(.*)}/', $targetPart, $matches);
                if(count($matches) > 0){
                    $attribute = $matches[1];
                    $params[$attribute] = $sourceParts[$index];
                    $i++;
                } else if($targetPart == $sourceParts[$index]){
                    $i++;
                }
            }

            if(count($targetParts) == $i){
                foreach($params as $key => $param){
                    $this->addParameter($key, $param);
                }
                return true;
            }
        }
        return null;
    }
}
