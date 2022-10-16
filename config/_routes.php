<?php
use application\Router;

/** Главная */
Router::get('/', '\app\controllers\site\MainController');

/** Админка */
Router::get('/admin', '\app\controllers\site\AdminController');

/** API */
Router::group('/api/v1', function() {

/** Чтение */
    Router::get([
                '/tree/{id}',
                '/object/{single}',
            ],
        '\app\controllers\api\v1\ObjectController'
    );

/** Аутентификация */
    Router::put('/login', '\app\controllers\api\v1\LoginController');

/** Редактирование) */
    Router::post('/add/{add:\d+}', '\app\controllers\api\v1\ModifyController');
    Router::put([
        '/edit/{edit:\d+}',
        '/jump/{jump:\d+}',
        ]
        ,'\app\controllers\api\v1\ModifyController');
    Router::delete('/{delete:\d+}', '\app\controllers\api\v1\ModifyController');
});
