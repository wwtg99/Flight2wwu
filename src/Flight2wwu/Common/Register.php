<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/5/7 0007
 * Time: 下午 6:50
 */

namespace Flight2wwu\Common;


use Flight2wwu\Component\Config\BaseConfig;
use Flight2wwu\Component\Utils\FormatUtils;
use Flight2wwu\Plugin\PluginManager;

class Register
{

    /**
     * @var Register
     */
    private static $instance = null;

    /**
     * @var BaseConfig
     */
    private $conf;

    /**
     * Register constructor.
     * @param BaseConfig $conf
     */
    public function __construct($conf)
    {
        $this->conf = $conf;
    }

    /**
     * @param BaseConfig $conf
     * @return Register
     */
    public static function getInstance($conf = null)
    {
        if (!self::$instance) {
            self::$instance = new Register($conf);
        }
        return self::$instance;
    }

    /**
     * Register all in config/register_config.php.
     */
    public function registerAll()
    {
        $tz = $this->conf->getConfig('timezone');
        date_default_timezone_set($tz);
        // register service
        $ser = $this->conf->getConfig('services');
        $this->registerService($ser);
        // register route
        $route_path = $this->conf->getConfig('route.path');
        $route_controller = $this->conf->getConfig('route.controller');
        $this->registerRoute($route_path);
        $this->registerRouteController($route_controller);
        // handler
        $handler_file = $this->conf->getConfig('handler');
        if ($handler_file && file_exists($handler_file)) {
            require "$handler_file";
        }
        // plugin
        $plugins = $this->conf->getConfig('plugin');
        $this->registerPlugin($plugins);
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
     * @param array $arr
     */
    public function registerRoute($arr)
    {
        if (!$arr || !is_array($arr)) {
            return;
        }
        foreach ($arr as $i => $v) {
            if (is_array($v) && count($v) > 1) {
                \Flight::route($v[0], $v[1]);
            }
        }
    }

    /**
     * Register route controllers.
     *
     * @param array $arr
     */
    public function registerRouteController($arr)
    {
        if (!$arr || !is_array($arr)) {
            return;
        }
        foreach ($arr as $k => $v) {
            $classname = $k . 'Controller';
            $ref = new \ReflectionClass($classname);
            if ($ref->isSubclassOf('Flight2wwu\Common\BaseController')) {
                $prefix = FormatUtils::formatWebPath($v);
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

    /**
     * Register plugins.
     *
     * @param array $arr
     * @return PluginManager|null
     */
    public function registerPlugin($arr)
    {
        if (!$arr || !is_array($arr)) {
            return null;
        }
        $pm = new PluginManager($arr);
        return $pm;
    }
}