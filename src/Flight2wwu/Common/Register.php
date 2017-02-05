<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/5/7 0007
 * Time: 下午 6:50
 */

namespace Wwtg99\Flight2wwu\Common;


class Register
{

    /**
     * @var Register
     */
    private static $instance = null;

    /**
     * @var string
     */
    private $baseUrl = '/';

    /**
     * Register constructor.
     */
    private function __construct()
    {

    }

    /**
     * @return Register
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Register();
        }
        return self::$instance;
    }

    /**
     * Register all in config/register_config.php.
     * @param \Wwtg99\Config\Common\IConfig $config
     */
    public function registerAll($config)
    {
        $this->baseUrl = $config->get('base_url');
        // register service
        $ser = $config->get('services');
        $this->registerService($ser);
        \Flight::Timer()->getNow();
        // handler
        $handler_file = $config->get('handler');
        if ($handler_file && file_exists($handler_file)) {
            include_once "$handler_file";
        }
    }

    /**
     * Register service.
     *
     * @param array $services
     */
    public function registerService($services)
    {
        if (is_array($services)) {
            foreach ($services as $k => $v) {
                \Flight::register($k, $v);
            }
        }
    }
//
//    /**
//     * Register routes.
//     *
//     * @param array $path
//     * @param array $controller
//     * @param array $restful
//     */
//    public function registerRoute($path, $controller, $restful)
//    {
//        $postPath = [];
//        if ($path && is_array($path)) {
//            foreach ($path as $p) {
//                if (is_array($p) && count($p) > 1) {
//                    $post = isset($p[2]) ? ($p[2] == 'post') : false;
//                    if ($post) {
//                        array_push($postPath, $p);
//                    } else {
//                        \Flight::route($p[0], $p[1]);
//                    }
//                }
//            }
//        }
//        if ($controller && is_array($controller)) {
//            foreach ($controller as $cls => $pref) {
//                $classname = $cls . 'Controller';
//                $ref = new \ReflectionClass($classname);
//                if ($ref->isSubclassOf('Wwtg99\Flight2wwu\Common\InstanceController')) {
//                    $ins = $ref->newInstance();
//                    $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);
//                    foreach ($methods as $method) {
//                        if (!$method->isStatic()) {
//                            $path = FormatUtils::formatWebPathArray([$pref, $method->getName()]);
//                            \Flight::route($path, [$ins, $method->getName()]);
//                        }
//                    }
//                } elseif ($ref->isSubclassOf('Wwtg99\Flight2wwu\Common\BaseController')) {
//                    $methods = $ref->getMethods(\ReflectionMethod::IS_STATIC);
//                    foreach ($methods as $m) {
//                        if ($m->isPublic()) {
//                            $path = FormatUtils::formatWebPathArray([$pref, $m->getName()]);
//                            \Flight::route($path, [$classname, $m->getName()]);
//                        }
//                    }
//                }
//            }
//        }
//        // restful
//        if ($restful && is_array($restful)) {
//            foreach ($restful as $cls => $pref) {
//                $classname = $cls . 'Controller';
//                $ref = new \ReflectionClass($classname);
//                if ($ref->isSubclassOf('Wwtg99\Flight2wwu\Common\RestfulInstanceController')) {
//                    $path = FormatUtils::formatWebPath($pref);
//                    $ins = $ref->newInstance();
//                    \Flight::route('GET ' . $path, [$ins, 'index']);
//                    \Flight::route('GET ' . $path . '/create', [$ins, 'create']);
//                    \Flight::route('POST ' . $path, [$ins, 'store']);
//                    \Flight::route('GET ' . $path . '/@id', [$ins, 'show']);
//                    \Flight::route('GET ' . $path . '/@id/edit', [$ins, 'edit']);
//                    \Flight::route('POST ' . $path . '/@id', [$ins, 'update']);
//                    \Flight::route('POST ' . $path . '/@id/destroy', [$ins, 'destroy']);
//                } elseif ($ref->isSubclassOf('Wwtg99\Flight2wwu\Common\RestfulController')) {
//                    $path = FormatUtils::formatWebPath($pref);
//                    \Flight::route('GET ' . $path, [$classname, 'index']);
//                    \Flight::route('GET ' . $path . '/create', [$classname, 'create']);
//                    \Flight::route('POST ' . $path, [$classname, 'store']);
//                    \Flight::route('GET ' . $path . '/@id', [$classname, 'show']);
//                    \Flight::route('GET ' . $path . '/@id/edit', [$classname, 'edit']);
//                    \Flight::route('POST ' . $path . '/@id', [$classname, 'update']);
//                    \Flight::route('POST ' . $path . '/@id/destroy', [$classname, 'destroy']);
//                }
//            }
//        }
//        if ($postPath) {
//            foreach ($postPath as $p) {
//                \Flight::route($p[0], $p[1]);
//            }
//        }
//    }
}