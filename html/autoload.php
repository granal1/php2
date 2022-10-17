<?php

spl_autoload_register(
    function ($class)
    {
        //var_dump($class);

        var_dump($class);
        //die();
        $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        if(file_exists($file)){
            require $file;
        }
    }
);

