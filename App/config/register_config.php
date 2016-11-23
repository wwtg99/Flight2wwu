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
        'Auth' => 'Wwtg99\Flight2wwu\Component\Auth\RoleAuth',
//        'View' => 'Wwtg99\Flight2wwu\Component\View\BorderView',
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
         * GET route/create | create | show create view
         * POST route | store | create new object
         * GET route/@id | show | show one object
         * GET route/@id/edit | edit | show edit view
         * PUT|PATCH route/@id | update | update object
         * DELETE route/@id | destroy | delete object
         */
        'path'=>[
//            ["*", array('\\Wwtg99\\App\\Controller\\DefaultController', 'rbac')],
//            ["*", array('\\Wwtg99\\App\\Controller\\DefaultController', 'language')],
            ['/', array('\\Wwtg99\\App\\Controller\\HomeController', 'home'), 'pre'],
            ["/home", array('\\Wwtg99\\App\\Controller\\HomeController', 'home')],
//            ["/403", array('\\Wwtg99\\App\\Controller\\DefaultController', 'forbidden'), 'pre'],
//            ["/405", array('\\Wwtg99\\App\\Controller\\DefaultController', 'methodNotAllowed'), 'pre'],
//            ["/changelog", array('\\Wwtg99\\App\\Controller\\HomeController', 'changelog'), 'pre'],
        ],
        /**
         * Override http method with header X-HTTP-Method-Override
         * All non-GET method will redirect to POST, get http method from X-HTTP-Method-Override
         */
        'override_http_method'=>true,
        /**
         * Register whole controller class with static public functions
         * full class name (without Controller) => prefix
         * All public static functions will register routes by /prefix/function
         * Controller must extends BaseController
         */
        'controller'=>[
            'Wwtg99\App\Controller\Auth'=>'/auth',
            'Wwtg99\App\Controller\OAuth'=>'/oauth',
            'Wwtg99\App\Controller\User'=>'/user',
//            'Wwtg99\App\Controller\Authorize'=>'/authorize', //oauth server
//            'Wwtg99\App\Controller\Admin\Admin'=>'/admin', //admin
        ],
        'restful'=>[
            /**
             * Register restful controller class
             * full class name (without Controller) => prefix
             * Class must extend Wwtg99\Flight2wwu\Common\RestfulController and overrides its methods.
             * Register 7 routes:
             * Method    Route                Action    Method name
             * Get       /prefix              index     index
             * Get       /prefix/create       create    create
             * Post      /prefix              store     store
             * Get       /prefix/id           show      show
             * Get       /prefix/edit/id      edit      edit
             * Post      /prefix/id           update    update
             * Post      /prefix/destroy/id   destroy   destroy
             */
//            'Wwtg99\App\Controller\Admin\Department'=>'/admin/department',//admin
//            'Wwtg99\App\Controller\Admin\Role'=>'/admin/role',//admin
//            'Wwtg99\App\Controller\Admin\User'=>'/admin/user',//admin
//            'Wwtg99\App\Controller\Admin\App'=>'/admin/app',//admin
        ]
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
        'user_edit'=>'auth/user_edit',
        'update_captcha'=>'auth/update_captcha',
        'user_center'=>'user/center',
        'admin'=>'admin/home',
    ],
    /**
     * Other handler functions or routes
     */
    'handler'=>CONFIG . 'handlers.php',
];