<?php
spl_autoload_register(function($class){
    if (\Frame::autoload($class)) {

    } else {
        $fp = str_replace(["\\", "/"], DIRECTORY_SEPARATOR, $class);
        if(file_exists("{$fp}.php")){
            require_once("{$fp}.php");
        }
    }

});
