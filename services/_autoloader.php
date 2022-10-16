<?php

    spl_autoload_register(function ($class) {

        $map = [
            __DIR__ . '/../',
            __DIR__ . '/../services/'
        ];
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
        foreach ($map as $dir) {
            $file = stream_resolve_include_path($dir . $path .'.php');
            if(is_readable($file)) {
                include_once $file;
                break;
            }
        }
    });