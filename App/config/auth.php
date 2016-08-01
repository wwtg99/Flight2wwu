<?php

return [
    'auth'=>[
        /**
         * Define role based access control
         * role_name => ['object or path' => auth]
         *
         * auth
         * 0: forbidden
         * 1: GET access or enable
         * 2: POST access
         * 3: 1 | 2
         *
         * For path
         * 1. check exact same path
         * 2. if not found, check upper dir /*
         * 3. if all upper dir is not found, check *
         */
        'rbac'=>[
            'anonymous'=>[
                '*' => 0,
                '/' => 3,
                '/403' => 3,
                '/auth/login' => 3,
                '/auth/logout' => 3,
                '/oauth/login' => 3,
                '/oauth/redirect_login' => 3,
            ],
            'admin' => [
                '*' => 3,
                '/admin/*' => 3,
            ],
            'common_user' => [
                '*' => 0,
                '/' => 3,
                '/403' => 3,
                '/admin/*' => 0,
                '/auth/*' => 3,
                '/oauth/*' => 3,
                '/view' => 1,
                '/comp' => 1,
                '/changelog' => 1,
            ],
        ],
        'session'=>false, //use session to store user info
        'cookie'=>false, //use cookie to store token
    ],
];