<?php

    include_once __DIR__ . '/../services/_autoloader.php';
    $routes = __DIR__ .'/../config/_routes.php';
    \application\App::run($routes);