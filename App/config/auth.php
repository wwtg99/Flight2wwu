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
         * 4: PUT | PATCH access
         * 8: DELETE access
         * 16: OPTIONS access
         * 32: HEAD access
         *
         * 63: for all access
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
                '/404' => 3,
                '/405' => 3,
                '/auth/login' => 3,
                '/auth/logout' => 3,
                '/auth/signup' => 3,
                '/auth/forget_password' => 3,
                '/auth/forget_change_password' => 3,
                '/auth/update_captcha' => 3,
                '/oauth/login' => 3,
                '/oauth/redirect_login' => 3,
                '/authorize/*'=>3,
                '/ajax'=>3,
                '/ajax_json'=>3,
            ],
            'admin' => [
                '*' => 63,
                '/admin/*' => 63,
            ],
            'common_user' => [
                '*' => 0,
                '/' => 3,
                '/403' => 3,
                '/404' => 3,
                '/405' => 3,
                '/admin/*' => 0,
                '/auth/*' => 3,
                '/user/*' => 3,
                '/oauth/*' => 3,
                '/changelog' => 1,
            ],
        ],
        'cookie'=>true, //use cookie to store token
        'cookie_expires'=>86400, // seconds for auth cookies expires
        "cache"=>[
            "type"=>"redis",  //apcu, file, redis, memcache or no cache
            "options"=>[
                "schema"=>"tcp",
                "host"=>"192.168.0.21",
                "database"=>6
            ]
        ],
        'token_ttl'=>7200,  // seconds for access_token expires
//        'token_only'=>true,  //enable access_token login, default false for normal login, true for oauth login
    ],
    'login_method'=>'normal',  //normal or oauth
    'access_token_login'=>false,  //enable login by access_token, usually for API and oauth server
    'access_token_key'=>'access_token',  //the key for access_token if enabled
    'oauth'=>[
        'code_uri'=>'http://localhost:8680/authorize/authorize',
        'token_uri'=>'http://localhost:8680/authorize/token',
        'redirect_uri'=>'http://localhost:8880/oauth/redirect_login',
        'redirect_uri_key'=>'redirect_uri',
        'app_id'=>'e94562cb12c6a74c2fe7b047d6995d70',
        'app_id_key'=>'client_id',
        'app_secret'=>'0e880f562e68eab825308f8921b3ac44',
        'app_secret_key'=>'client_secret',
    ],
];