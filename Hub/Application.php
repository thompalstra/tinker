<?php
namespace Hub;

use PDO;
use Exception;

use Frame;

use Hub\Base\Request;
use Hub\Base\Base;
use Hub\Base\Controller;
use Hub\Http\View;

class Application extends Base
{

    protected $input;

    public $root;
    public $routes = [
        "names" => []
    ];
    public $db;
    public $queues = [];

    public function __construct()
    {
        require_once("Frame.php");
        Frame::$app = &$this;

        $this->root = dirname(__DIR__);

        $config = Frame::path([$this->root, 'config.php']);
        if(file_exists($config)){
            $this->config = require($config);
        }

        $host = $this->config['mysql']['host'];
        $dbname = $this->config['mysql']['dbname'];
        $user = $this->config['mysql']['user'];
        $password = $this->config['mysql']['password'];

        $this->db = new PDO("mysql:host={$host};dbname={$dbname}", "{$user}", "{$password}");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->controller = new Controller();

        View::registerEngine("Hub\Blade\Renderer@output", ["blade", "blade.php"]);
        View::registerEngine("Hub\Http\Renderer@output", ["html", "php"]);
    }

    public function run($request)
    {
        return $this->handle($this->parse($request));
    }

    public function parse($request)
    {
        if($request instanceof Request){
            $this->process($request);
            return $this->request->getRoute();
        } else {
            throw new Exception("Request must be of instance 'Hub\Base\Request'");
        }
    }

    public function process($request)
    {
        $this->request = $request;
        $this->input = new \Hub\Base\Input();

        $config = $this->request->getRoutes();
        if(file_exists($config)){
            require("$config");
        } else {
            trigger_error("Missing required file '$config'", E_USER_ERROR);
        }
    }

    public function handle($route)
    {
        preg_match('/(.*)@(.*)/', $route[0], $matches);
        if (!empty($matches)) {
            $controller = $matches[1];
            $method = $matches[2];

            $controller = Frame::$app->controller->resolve($controller);

            if(class_exists($controller)){
                Frame::$app->controller = new $controller();
                Frame::$app->controller->run($method, $route[1]);
            } else {
                return Frame::$app->controller->run("error", ['message' => "{$controller} does not exist."]);
            }
        } else {
            return Frame::$app->controller->run("error", ['message' => "Route '{$route[0]}' does not exist."]);
        }
    }
}
