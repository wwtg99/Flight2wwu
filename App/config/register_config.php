<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/5/7 0007
 * Time: 下午 7:04
 */

//register files
return [
    /**
     * Register class to Flight, Flight()::name to use
     * name => full class name
     * Class must have a non-parameter constructor
     */
    'services'=>[
        'Timer' => 'Wwtg99\Flight2wwu\Component\Utils\Timer',
        'Router' => 'Wwtg99\Flight2wwu\Common\Router',
        'Auth' => 'Wwtg99\Flight2wwu\Component\Auth\RBACAuth',
        'View' => 'Wwtg99\Flight2wwu\Component\View\TwigView',
        'Log' => 'Wwtg99\Flight2wwu\Component\Log\Monolog',
        'DB' => 'Wwtg99\Flight2wwu\Component\Database\MedooDB',
        'Redis' => 'Wwtg99\Flight2wwu\Component\Database\PRedis',
        'DataPool' => 'Wwtg99\Flight2wwu\Component\Database\DataPool',
        'Locale' => 'Wwtg99\Flight2wwu\Component\Translation\SymTrans',
        'Cache' => 'Wwtg99\Flight2wwu\Component\Storage\Cache',
        'Session' => 'Wwtg99\Flight2wwu\Component\Storage\SessionUtil',
        'Cookie' => 'Wwtg99\Flight2wwu\Component\Storage\CookieUtil',
        'Value' => 'Wwtg99\Flight2wwu\Component\Storage\OldValue',
        'Assets' => 'Wwtg99\Flight2wwu\Component\View\AssetsManager',
        'Captcha' => 'Wwtg99\Flight2wwu\Component\Utils\Captcha',
        'CSRF' => 'Wwtg99\Flight2wwu\Component\Utils\CSRFCode',
        'Mail' => 'Wwtg99\Flight2wwu\Component\Utils\Mail',
        'Express' => 'Wwtg99\Flight2wwu\Component\Utils\Express',
        'Plugin' => 'Wwtg99\Flight2wwu\Component\Plugin\PluginManager',
    ],
    /**
     * Register route here
     * All path will be registered in sequence
     */
    'route'=>[
        /**
         * Route path for functions
         * [route, array(full class name, function name)] or
         * [route, controller class name<, options>] or
         * [route, function]
         * First param: route / or specify method: GET / or GET|POST /
         * Second param: array should be class name and public function name, controller class name should not contain last "Controller"
         * Third param: set option to "restful" to register Restful controller
         *
         * Restful Controller register methods:
         * GET route | index | List all objects
         * POST route | store | create new object
         * GET route/@id | show | show one object
         * PUT|PATCH route/@id | update | update object
         * DELETE route/@id | destroy | delete object
         *
         * If third param is restful+, two more routes will be registered
         * GET route/create | create | show create view
         * GET route/@id/edit | edit | show edit view
         */
        'path'=>[
            ["*", array('\\Wwtg99\\App\\Controller\\DefaultController', 'rbac')],
            ["*", array('\\Wwtg99\\App\\Controller\\DefaultController', 'language')],
            ["/403", array('\\Wwtg99\\App\\Controller\\DefaultController', 'forbidden')],
            ["/405", array('\\Wwtg99\\App\\Controller\\DefaultController', 'methodNotAllowed')],
            ['/', array('\\Wwtg99\\App\\Controller\\HomeController', 'home')],
            ["/home", array('\\Wwtg99\\App\\Controller\\HomeController', 'home')],
            ["/changelog", array('\\Wwtg99\\App\\Controller\\HomeController', 'changelog')],
            ["/auth", 'Wwtg99\App\Controller\Auth'],
            ["/user", 'Wwtg99\App\Controller\User'],
            ["/oauth", 'Wwtg99\App\Controller\OAuth'],
            //oauth server
            ["/authorize", 'Wwtg99\App\Controller\Authorize'],
            //admin
            ['/admin', 'Wwtg99\App\Controller\Admin\Admin'],
            ["/admin/departments", 'Wwtg99\App\Controller\Admin\Department', 'restful+'],
            ["/admin/roles", 'Wwtg99\App\Controller\Admin\Role', 'restful+'],
            ["/admin/users", 'Wwtg99\App\Controller\Admin\User', 'restful+'],
            ["/admin/apps", 'Wwtg99\App\Controller\Admin\App', 'restful+'],
        ],
        /**
         * Override http method with header X-HTTP-Method-Override
         * All non-GET method will redirect to POST, get http method from X-HTTP-Method-Override
         */
        'override_http_method'=>true,
    ],
    /**
     * Special routes (login, logout, admin) defined here.
     */
    'defined_routes'=>[
        'login'=>'auth/login',
//        'login'=>'oauth/login', //oauth
        'logout'=>'auth/logout',
        'signup'=>'auth/signup',
        'info'=>'auth/info',
        'change_password'=>'auth/password',
        'forget_password'=>'auth/forget_password',
        'user_edit'=>'user/edit',
        'update_captcha'=>'auth/update_captcha',
        'user_center'=>'user/center',
        'admin'=>'admin/home',
    ],
    /**
     * Other handler functions or routes
     */
    'handler'=>CONFIG . 'handlers.php',
];