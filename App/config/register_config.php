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
     * Class must implement ServiceProvider
     */
    'services'=>[
        'Auth' => 'Wwtg99\Flight2wwu\Component\Auth\RoleAuth',
        'View' => 'Wwtg99\Flight2wwu\Component\View\BorderView',
//        'View' => 'Wwtg99\Flight2wwu\Component\View\TwigView',
        'Log' => 'Wwtg99\Flight2wwu\Component\Log\Monolog',
//        'DB' => 'Flight2wwu\Component\Database\MedooDB',
//        'DB' => 'Wwtg99\Flight2wwu\Component\Database\MedooPool',
//        'ORM' => 'Wwtg99\Flight2wwu\Component\Database\OrmManager',
//        'DataPool' => 'Wwtg99\Flight2wwu\Component\Database\DataPool',
//        'Locale' => 'Wwtg99\Flight2wwu\Component\Translation\SymTrans',
//        'Cache' => 'Wwtg99\Flight2wwu\Component\Storage\Cache',
//        'Session' => 'Wwtg99\Flight2wwu\Component\Storage\SessionUtil',
//        'Cookie' => 'Wwtg99\Flight2wwu\Component\Storage\CookieUtil',
//        'Value' => 'Wwtg99\Flight2wwu\Component\Storage\OldValue',
        'Assets' => 'Wwtg99\Flight2wwu\Component\View\AssetsManager',
//        'Mail' => 'Wwtg99\Flight2wwu\Component\Utils\Mail',
//        'Express' => 'Wwtg99\Flight2wwu\Component\Utils\Express',
    ],
    /**
     * Register route here
     * All path will be registered in sequence
     */
    'route'=>[
        /**
         * Route path for functions
         * [route, array(full class name, function name), pre]
         * pre: path will be registered before controller, post: path will be registered after controller
         */
        'path'=>[
//            ["*", array('\\Wwtg99\\App\\Controller\\DefaultController', 'rbac'), 'pre'],
//            ["*", array('\\Wwtg99\\App\\Controller\\DefaultController', 'language'), 'pre'],
            ['/', array('\\Wwtg99\\App\\Controller\\HomeController', 'home'), 'pre'],
            ["/home", array('\\Wwtg99\\App\\Controller\\HomeController', 'home'), 'pre'],
            ["/403", array('\\Wwtg99\\App\\Controller\\DefaultController', 'forbidden'), 'pre'],
            ["/changelog", array('\\Wwtg99\\App\\Controller\\HomeController', 'changelog'), 'pre'],
        ],
        /**
         * Register whole controller class with static public functions
         * full class name (without Controller) => prefix
         * All public static functions will register routes by /prefix/function
         * Controller must extends BaseController
         */
        'controller'=>[
//            '\Wwtg99\App\Controller\Home'=>'h',
//            'Wwtg99\App\Controller\Auth'=>'auth',
//            'Wwtg99\App\Controller\OAuth'=>'oauth',
//            'Wwtg99\App\Controller\Admin'=>'admin',
        ],
    ],
    /**
     * Other handler functions or routes
     */
    'handler'=>CONFIG . 'handlers.php',
];