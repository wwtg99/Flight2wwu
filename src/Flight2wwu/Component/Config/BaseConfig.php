<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/7/15
 * Time: 11:42
 */

namespace Flight2wwu\Component\Config;

abstract class BaseConfig
{

    /**
     * @var array
     */
    protected $conf = [];

    /**
     * BaseConfig constructor.
     * @param array $conf
     */
    public function __construct(array $conf = [])
    {
        $this->conf = $conf;
    }

    /**
     * Get config by name.
     * Support .(dot) in name to search in child node. (a.b.c search for $conf['a']['b']['c'])
     *
     * @param string $name
     * @param $defval
     * @return mixed
     */
    public function getConfig($name, $defval = null)
    {
        return $this->getChildNode($this->conf, $name, $defval);
    }

    /**
     * @param string $name
     * @param $value
     * @return mixed
     */
    public function setConfig($name, $value)
    {
        if ($name) {
            $this->conf[$name] = $value;
            return $value;
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function export()
    {
        return $this->conf;
    }

    /**
     * @param array $arr
     * @param string $name
     * @param $defval
     * @return mixed
     */
    protected function getChildNode($arr, $name, $defval = null)
    {
        $dot = strpos(trim($name), '.');
        if ($dot > 0) {
            $key = substr($name, 0, $dot);
            $rname = substr($name, $dot + 1);
        } else {
            $key = $name;
            $rname = null;
        }
        if (is_array($arr)) {
            if (isset($arr[$key])) {
                if (is_null($rname)) {
                    return $arr[$key];
                } elseif (is_array($arr[$key])) {
                    return $this->getChildNode($arr[$key], $rname);
                }
            }
        }
        return $defval;
    }
}