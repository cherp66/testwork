<?php

    return [

        'ObjectController' => [
            '1_Single' => '\app\actions\tree\Single',
            '2_Tree' => '\app\actions\tree\Tree',
        ],

        'LoginController' => [
            '1_Logout' => '\app\actions\login\Logout',
            '2_Validator' => '\app\actions\login\Validator',
            '3_Login' => '\app\actions\login\Login',
        ],

        'ModifyController' => [
            '1_Auth' => '\app\actions\modify\Dispatcher',
            '2_Validator' => '\app\actions\modify\Validator',
            '3_Add' => '\app\actions\modify\Add',
            '4_Edit' => '\app\actions\modify\Edit',
            '5_Jump' => '\app\actions\modify\Jump',
            '6_Delete' => '\app\actions\modify\Delete',
        ],
    ];
