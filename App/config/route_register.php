<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/6
 * Time: 16:43
 */

//Register route here
//[route, array(full class name, function name)]

return [
    ["*", array('\\App\\Controller\\HomeController', 'rbac')],
    ["*", array('\\App\\Controller\\HomeController', 'access')],
    ["*", array('\\App\\Controller\\HomeController', 'language')],
    ["/", array('\\App\\Controller\\HomeController', 'home')],
    ["/home", array('\\App\\Controller\\HomeController', 'home')],
    ["/403", array('\\App\\Controller\\HomeController', 'forbidden')],
    ["/changelog", array('\\App\\Controller\\HomeController', 'changelog')],
];