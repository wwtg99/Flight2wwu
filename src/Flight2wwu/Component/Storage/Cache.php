<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/29
 * Time: 16:05
 */

namespace Wwtg99\Flight2wwu\Component\Storage;

use Desarrolla2\Cache\Adapter\AbstractAdapter;
use Desarrolla2\Cache\Adapter\Apcu;
use Desarrolla2\Cache\Adapter\File;
use Wwtg99\Flight2wwu\Common\FWException;

class Cache implements IAttribute
{

    /**
     * @var \Desarrolla2\Cache\Cache
     */
    private $cache;

    /**
     * Cache constructor.
     * @param array $conf
     */
    public function __construct($conf = [])
    {
        if (!$conf) {
            $conf = \Flight::get('config')->get('storage');
        }
        $this->loadConfig($conf);
    }

    /**
     * @param array $conf
     */
    public function loadConfig(array $conf)
    {
        if (isset($conf['cache'])) {
            $c = $conf['cache'];
            if (isset($c['adapter']) && isset($c['params'])) {
                $adapter = $c['adapter'];
                $params = $c['params'];
                $this->initAdapter($adapter, $params);
            }
        }
    }

    /**
     * @return \Desarrolla2\Cache\Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        if ($this->cache) {
            return $this->cache->get($name);
        }
        return null;
    }

    /**
     * @param string $name
     * @param $val
     * @param int $expire
     * @return IAttribute
     */
    public function set($name, $val, $expire = null)
    {
        if ($this->cache) {
            $this->cache->set($name, $val, $expire);
        }
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        if ($this->cache) {
            return $this->cache->has($name);
        }
        return false;
    }

    /**
     * @param string $name
     * @return IAttribute
     */
    public function delete($name)
    {
        if ($this->cache) {
            $this->cache->delete($name);
        }
        return $this;
    }

    /**
     * @param string $adapter
     * @param array $params
     * @throws FWException
     */
    private function initAdapter($adapter, array $params)
    {
        switch($adapter) {
            case 'Apcu': $adapter = $this->initApcu($params); break;
            case 'File': $adapter = $this->initFile($params); break;
            default:
                throw new FWException("Adapter $adapter is not supported", 1);
        }
        $this->cache = new \Desarrolla2\Cache\Cache($adapter);
    }

    /**
     * @param array $params
     * @return Apcu
     */
    private function initApcu(array $params)
    {
        $adapter = new Apcu();
        $this->setOption($adapter, $params);
        return $adapter;
    }

    /**
     * @param array $params
     * @return File
     */
    private function initFile(array $params)
    {
        if (array_key_exists('cache_dir', $params)) {
            $cd = $params['cache_dir'];
            unset($params['cache_dir']);
        } else {
            $cd = null;
        }
        if (!file_exists($cd)) {
            mkdir($cd, 0777, true);
        }
        $adapter = new File($cd);
        $this->setOption($adapter, $params);
        return $adapter;
    }

    /**
     * @param AbstractAdapter $adapter
     * @param array $params
     * @return AbstractAdapter
     */
    private function setOption(AbstractAdapter $adapter, array $params)
    {
        foreach ($params as $k => $v) {
            $adapter->setOption($k, $v);
        }
        return $adapter;
    }
} 