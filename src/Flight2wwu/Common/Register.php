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

}