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
                '/auth/signup' => 3,
                '/oauth/login' => 3,
                '/oauth/redirect_login' => 3,
                '/ajax'=>3,
                '/ajax_json'=>3,
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
                '/changelog' => 1,
            ],
        ],
        'session'=>true, //use session to store user info
        'session_expires'=>60, // seconds for auth session expires
        'cookie'=>true, //use cookie to store token
        'cookie_expires'=>86400, // seconds for auth cookies expires
    ],
    'oauth'=>[
        'code_uri'=>'http://localhost:7280/authorize/authorize',
        'token_uri'=>'http://localhost:7280/authorize/token',
        'redirect_uri'=>'http://localhost:8880/oauth/redirect_login',
        'redirect_uri_key'=>'redirect_uri',
        'app_id'=>'c7150743be21e9df63c1d5a50049360d',
        'app_id_key'=>'client_id',
        'app_secret'=>'9f2950219ba7b4a3773ba9bd2bab0941',
        'app_secret_key'=>'client_secret',
    ],
];