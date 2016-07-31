<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/5/7 0007
 * Time: 下午 6:50
 */

namespace Wwtg99\Flight2wwu\Common;



use Wwtg99\Flight2wwu\Component\Utils\FormatUtils;
use Wwtg99\Flight2wwu\Plugin\PluginManager;

class Register
{

    /**
     * @var Register
     */
    private static $instance = null;

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
        $tz = $config->get('timezone');
        date_default_timezone_set($tz);
        // register service
        $ser = $config->get('services');
        $this->registerService($ser);
        // register route
        $route_path = $config->get('route.path');
        $route_controller = $config->get('route.controller');
        $this->registerRoute($route_path, $route_controller);
        // handler
        $handler_file = $config->get('handler');
        if ($handler_file && file_exists($handler_file)) {
            include_once "$handler_file";
        }
    }

    /**
     * Register service.
     *
     * @param array $arr
     */
    public function registerService($arr)
    {
        if (!$arr || !is_array($arr)) {
            return;
        }
        $boot = [];
        //register
        foreach ($arr as $k => $v) {
            \Flight::register($k, $v);
            $ins = \Flight::$k();
            if ($ins instanceof ServiceProvider) {
                $ins->register();
                array_push($boot, $k);
            }
        }
        //boot
        foreach ($boot as $k) {
            $ins = \Flight::$k();
            if ($ins instanceof ServiceProvider) {
                $ins->boot();;
            }
        }
    }

    /**
     * Register routes.
     *
     * @param array $path
     * @param array $controller
     */
    public function registerRoute($path, $controller)
    {
        $postPath = [];
        if ($path && is_array($path)) {
            foreach ($path as $p) {
                if (is_array($p) && count($p) > 1) {
                    $post = isset($p[2]) ? ($p[2] == 'post') : false;
                    if ($post) {
                        array_push($postPath, $p);
                    } else {
                        getLog()->warning('-------1', $p);
                        \Flight::route($p[0], $p[1]);
                    }
                }
            }
        }
        if ($controller && is_array($controller)) {
            foreach ($controller as $cls => $pref) {
                $classname = $cls . 'Controller';
                $ref = new \ReflectionClass($classname);
                if ($ref->isSubclassOf('Wwtg99\Flight2wwu\Common\BaseController')) {
                    $prefix = FormatUtils::formatWebPath($pref);
                    $methods = $ref->getMethods(\ReflectionMethod::IS_STATIC);
                    foreach ($methods as $m) {
                        if ($m->isPublic()) {
                            $path = $prefix . $m->getName();
                            \Flight::route($path, [$classname, $m->getName()]);
                        }
                    }
                }
            }
        }
        if ($postPath) {
            foreach ($postPath as $p) {
                \Flight::route($p[0], $p[1]);
            }
        }
    }

    /**
     * Register plugins.
     *
     * @param array $plugins
     * @return PluginManager|null
     */
    public function registerPlugin($plugins)
    {
        if ($plugins && is_array($plugins)) {
            $pm = new PluginManager($plugins);
            return $pm;
        }
        return null;
    }
}