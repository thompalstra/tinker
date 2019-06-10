<?php
namespace Hub\Http;

use Frame;

class Request extends \Hub\Base\Request
{
    public function process(array $options = [])
    {
        $path = $options["REQUEST_URI"];

        if($path == "/"){
            $path = "";
        } else {
            $path = substr($path, 1, strlen($path));
        }

        if(strpos($path, "?") > -1){
            $path = substr($path, 0, strpos("?", $path));
        }

        $this->setPath($path);

        $method = $_SERVER['REQUEST_METHOD'];
        $method = strtolower($method);

        $this->setMethod($method);
    }

    public function getRoutes()
    {
        return Frame::path(["routes", "web.php"]);
    }

    public function getControllerPath()
    {
        return Frame::path(["App","Http","Controllers"]);
    }

    public function getViewPath()
    {
        return Frame::path([]);
    }

    public function getLayoutPath()
    {
        return Frame::path([]);
    }
}
