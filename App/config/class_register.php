<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/6
 * Time: 16:42
 */

//Register class here
//name => full class name
//Flight();:name to use

return [
    'Auth' => 'Flight2wwu\Component\Auth\RoleAuth',
//    'Rbac' => 'Flight2wwu\Component\Auth\RoleBasedAccessControl',
    'View' => 'Flight2wwu\Component\View\BorderView',
    'Log' => 'Flight2wwu\Component\Log\Monolog',
//    'DB' => 'Flight2wwu\Component\Database\PdoDB',
    'DB' => 'Flight2wwu\Component\Database\MedooDB',
    'Locale' => 'Flight2wwu\Component\Translation\SymTrans',
    'Cache' => 'Flight2wwu\Component\Storage\Cache',
    'Session' => 'Flight2wwu\Component\Storage\SessionUtil',
    'Cookie' => 'Flight2wwu\Component\Storage\CookieUtil',
    'Value' => 'Flight2wwu\Component\Storage\OldValue',
    'Assets' => 'Flight2wwu\Component\View\AssetsManager',
    'Mail' => 'Flight2wwu\Component\Utils\Mail',
    'Express' => 'Flight2wwu\Component\Utils\Express',
];