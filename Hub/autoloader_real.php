<?php
spl_autoload_register(function($class){
    $fp = str_replace(["\\", "/"], DIRECTORY_SEPARATOR, $class);
    if(file_exists("{$fp}.php")){
        require_once("{$fp}.php");
    }
});
