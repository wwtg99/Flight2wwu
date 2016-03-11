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
    'Rbac' => 'Flight2wwu\Component\Auth\RoleBasedAccessControl',
    'View' => 'Flight2wwu\Component\View\BorderView',
    'Log' => 'Flight2wwu\Component\Log\Monolog',
//    'DB' => 'Flight2wwu\Component\Database\PdoDB',
    'DB' => 'Flight2wwu\Component\Database\MedooDB',
    'Locale' => 'Flight2wwu\Component\Translation\SymTrans',
    'Value' => 'Flight2wwu\Component\Session\LastValue',
    'Assets' => 'Flight2wwu\Component\View\AssetsManager',
    'Cache' => 'Flight2wwu\Component\Session\Cache',
    'Mail' => 'Flight2wwu\Component\Utils\Mail',
    'Express' => 'Flight2wwu\Component\Utils\Express',
];