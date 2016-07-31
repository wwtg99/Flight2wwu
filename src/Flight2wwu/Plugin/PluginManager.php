<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/4
 * Time: 9:40
 */

namespace Wwtg99\Flight2wwu\Plugin;


use Wwtg99\Flight2wwu\Common\ServiceProvider;

class PluginManager implements ServiceProvider
{

    /**
     * @var PluginManager
     */
    private static $instance = null;

    /**
     * @var array
     */
    private $plugins = [];

    /**
     * @var array
     */
    private $enables = [];

    /**
     * @var array
     */
    private $conf = [];


    /**
     * Called after register.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Called after all class is registered.
     *
     * @return void
     */
    public function boot()
    {
        $plugins = \Flight::get('config')->get('plugin');
        if ($plugins) {
            $this->plugins = $plugins;
            foreach ($this->plugins as $id => $p) {
                if ($p['enabled']) {
                    $this->enable($id);
                }
            }
        }
    }

    /**
     * @return PluginManager
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * @param $id
     * @return null|array
     */
    public function getPluginConfig($id = null)
    {
        if (is_null($id)) {
            return $this->plugins;
        } else {
            if (array_key_exists($id, $this->plugins)) {
                return $this->plugins[$id];
            }
        }
        return null;
    }

    /**
     * @param $id
     * @param array $config
     */
    public function setPluginConfig($id, array $config)
    {
        if (!is_null($id) && $config) {
            $this->plugins[$id] = $config;
        }
    }

    /**
     * @param $server_name
     * @return null|IPlugin
     */
    public function getPlugin($server_name)
    {
        if (array_key_exists($server_name, $this->enables)) {
            return $this->enables[$server_name][1];
        }
        return null;
    }

    /**
     * @param $id
     */
    public function enable($id)
    {
        $p = $this->getPluginConfig($id);
        $name = $p['server_name'];
        $cls = $p['class_name'];
        $nm = $p['name'];
        try {
            $ins = new \ReflectionClass($cls);
            $ins = $ins->newInstance();
            if ($ins instanceof IPlugin) {
                $this->enables[$name] = [$id, $ins];
                $p['enabled'] = true;
                $this->setPluginConfig($id, $p);
            }
        } catch (\Exception $e) {
            getLog()->warning("unable to start plugin $nm");
        }
    }

    /**
     * @param $id
     */
    public function disable($id)
    {
        $p = $this->getPluginConfig($id);
        $name = $p['server_name'];
        $p['enabled'] = false;
        $this->setPluginConfig($id, $p);
        if (array_key_exists($name, $this->enables)) {
            if ($this->enables[$name][0] == $id) {
                unset($this->enables[$name]);
            }
        }
    }

    /**
     * Temporary disable server
     *
     * @param $server_name
     */
    public function disable_server($server_name)
    {
        if (array_key_exists($server_name, $this->enables)) {
            unset($this->enables[$server_name]);
        }
    }

    /**
     * List enabled plugin id
     *
     * @return array
     */
    public function list_enables()
    {
        $enables = [];
        foreach ($this->enables as $sname => $server) {
            array_push($enables, $server[0]);
        }
        return $enables;
    }

    /**
     * Write config file
     */
    public function writeConfig()
    {
        if ($this->$conf) {
            file_put_contents($this->$conf, json_encode($this->plugins, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
        }
    }
} 