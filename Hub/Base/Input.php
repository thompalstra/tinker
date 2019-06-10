<?php

namespace Hub\Base;

use Frame;

class Input extends \Hub\Base\Base
{

    protected $input = [];

    public function __construct()
    {
        switch (strtolower(Frame::$app->request->getMethod())) {
            case "get":
                $this->input = $_GET;
            break;
            case "delete":

            break;
            case "post":
                $this->input = $_POST;
            break;
            case "put":
                switch (Frame::$app->request->getContentType()) {
                    case "application/x-www-form-urlencoded":
                        parse_str(file_get_contents('php://input'), $input);
                        $this->input = $input;
                    break;

                    case "application/json":
                        $this->input = json_decode(file_get_contents('php://input'));
                    break;
                }

            break;
        }
        // $this->input = $input;
    }

    public function get($field)
    {
        return $this->input[$field];
    }

    public function has($field)
    {
        return isset($this->input[$field]);
    }

    public function all()
    {
        return $this->input;
    }
}
