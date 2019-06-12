<?php
namespace Hub\Base;

use Frame;

class Request extends Base implements RequestInterface
{
    protected $path = "";
    protected $parameters = [];
    protected static $fields;

    public function __construct(array $options = [])
    {
        $this->process($options);

        $this->setHost($_SERVER["HTTP_HOST"]);
    }

    public function getContentType()
    {
        return isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : null;
    }

    public static function get(string $field)
    {
        return Frame::$app->input->get($field);
    }

    public static function has(string $field)
    {
        return Frame::$app->input->has($field);
    }

    public static function all()
    {
        return Frame::$app->input->all();
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
        // var_dump($_SERVER); die;
        foreach (Frame::$app->routes as $host => $routes) {
            if ($host == "names") { continue; }
            if ($this->matchHost($host)) {
                foreach ($routes[$this->getMethod()] as $path => $controller) {
                    if($this->matchRoute($this->getPath(), $path)){
                        return [Frame::path([$controller]), $this->getParameters()];
                    }
                }
            }
        }
    }

    public function matchHost($host)
    {
        if ($host == "*") {
            return true;
        }
        if ($host == $this->getHost()) {
            return true;
        }
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
