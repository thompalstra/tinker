<?php
namespace Hub\Http;

class Renderer extends \Hub\Base\Base
{
    public static function output($fp, $data)
    {
        extract($data);
        ob_start();
        include($fp);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}
