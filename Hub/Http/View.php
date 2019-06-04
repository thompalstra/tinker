<?php
// namespace Hub\Http;

use Hub\Base\Base;

class View extends Base
{

    // public static $extensions = ["php", "html", "php.blade", "blade", "twig"];

    public static $engines = [];

    public static function registerEngine($engine, $method)
    {
        self::$engines[$engine] = $method;
    }

    public static function getEngines()
    {
        return self::$engines;
    }

    public static function render($name, $data)
    {
        $viewPath = Frame::$app->request->getViewPath();
        $layoutPath = Frame::$app->request->getLayoutPath();

        $layout = Frame::path([$layoutPath, Frame::$app->controller->getLayout()]);
        $view = Frame::path([$viewPath, $name]);

        echo self::make($layout, [
            "content" => self::make($view, $data)
        ]);
        exit();
    }

    public static function make($name, $data = [])
    {
        $name = "storage/views/{$name}";
        foreach(self::getEngines() as $renderer => $extensions){
            foreach($extensions as $extension){
                if(file_exists("{$name}.{$extension}")){
                    preg_match('/(.*)@(.*)/', $renderer, $matches);

                    $class = $matches[1];
                    $method = $matches[2];

                    return call_user_func_array([$class, $method], ["{$name}.{$extension}", $data]);
                }
            }
        }
        return "unable to render file: '{$name}'";
    }
}
