<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/16
 * Time: 11:33
 */

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

return [
    'anonymous'=>[
        '/' => 3,
        '/403' => 3,
        '/auth/login' => 3
    ],
    'admin' => [
        '*' => 3
    ],
    'common_user' => [
        '*' => 0,
        '/admin/*' => 0,
        '/auth/*' => 3,
        '/view' => 1,
        '/changelog' => 1,
    ],
];