<?php

    return [
        'migrations' => [
            'language' => 'Ru',
            'db' => (new \config\Main)->dbParams(),
            'namespace' => 'console\migrations',
            'dir' => __DIR__ .'/migrations/',
        ],
    ];