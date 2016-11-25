<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/11/23
 * Time: 11:27
 */

namespace Wwtg99\Flight2wwu\Common;


class Router
{

    /**
     * @var bool
     */
    protected $overrideHttpMethod = false;

    /**
     * @var array
     */
    protected $routes = [];

    /**
     * Router constructor.
     * @param array $conf
     */
    public function __construct($conf = [])
    {
        if (!$conf) {
            $conf = \Flight::get('config')->get('route');
        }
        $this->loadConfig($conf);
    }

    /**
     * @param array $conf
     */
    public function loadConfig(array $conf)
    {
        $this->overrideHttpMethod = isset($conf['override_http_method']) ? $conf['override_http_method'] : false;
        $this->routes = isset($conf['path']) ? $conf['path'] : [];
    }

    /**
     * @param array $routes
     */
    public function registerRoutes(array $routes = [])
    {
        if (!$routes) {
            $routes = $this->routes;
        }
        foreach ($routes as $route) {
            if (is_array($routes) && count($route) > 1) {
                $path = $route[0];
                $option = isset($route[2]) ? $route[2] : null;
                if (is_array($route[1])) {
                    $this->registerArrayRoute($path, $route[1], $option);
                } elseif (is_string($route[1])) {
                    $this->registerStringRoute($path, $route[1], $option);
                } elseif (is_callable($route[1])) {
                    $this->registerCallableRoute($path, $route[1], $option);
                }
            }
        }
    }

    /**
     * @param $path
     * @param $route
     * @param $option
     */
    public function registerStringRoute($path, $route, $option = null)
    {
        $rest = false;
        $restplus = false;
        if ($option == 'restful' || $option == 'restful+') {
            $rest = true;
            $restplus = ($option == 'restful+');
        }
        $classname = $route . 'Controller';
        $ref = new \ReflectionClass($classname);
        if ($rest) {
            if ($ref->isSubclassOf('Wwtg99\Flight2wwu\Component\Controller\RestfulController')) {
                $ins = $ref->newInstance();
                $path = rtrim($path, '/');
                \Flight::route("GET $path", [$ins, 'index']);
                \Flight::route("POST $path", [$ins, 'store']);
                if ($restplus) {
                    \Flight::route("GET $path/create", [$ins, 'create']);
                    \Flight::route("GET $path/@id/edit", [$ins, 'edit']);
                }
                \Flight::route("GET $path/@id", [$ins, 'show']);
                if ($this->overrideHttpMethod) {
                    \Flight::route("POST|PUT|PATCH $path/@id", [$ins, 'update']);
                    \Flight::route("POST|DELETE $path/@id", [$ins, 'destroy']);
                } else {
                    \Flight::route("PUT|PATCH $path/@id", [$ins, 'update']);
                    \Flight::route("DELETE $path/@id", [$ins, 'destroy']);
                }
            }
        } elseif ($ref->isSubclassOf('Wwtg99\Flight2wwu\Component\Controller\BaseController')) {
            $ins = $ref->newInstance();
            $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);
            $path = rtrim($path, '/') . '/';
            foreach ($methods as $method) {
                $p = $path . $method->getName();
                \Flight::route($p, [$ins, $method->getName()]);
            }
        }
    }

    /**
     * @param $path
     * @param array $route
     * @param $option
     */
    public function registerArrayRoute($path, $route, $option = null)
    {
        \Flight::route($path, $route);
    }

    /**
     * @param $path
     * @param callable $route
     * @param $option
     */
    public function registerCallableRoute($path, $route, $option = null)
    {
        \Flight::route($path, $route);
    }

}