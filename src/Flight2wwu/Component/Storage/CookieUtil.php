<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/11
 * Time: 11:36
 */

namespace Wwtg99\Flight2wwu\Component\Storage;


class CookieUtil implements IAttribute
{

    /**
     * @var bool
     */
    private $enabled = false;

    /**
     * @var array
     */
    private $cookies = [];

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $path = '/';

    /**
     * @var string
     */
    private $domain = null;

    /**
     * CookieUtil constructor.
     * @param array $conf
     */
    public function __construct($conf = [])
    {
        if (!$conf) {
            $conf = \Flight::get('config')->get('storage');
        }
        $this->prefix = isset($conf['prefix']) ? $conf['prefix'] . '_' : '';
        $this->path = isset($conf['cookie_path']) ? $conf['cookie_path'] : '/';
        $this->domain = isset($conf['cookie_domain']) ? $conf['cookie_domain'] : null;
        $enabled = isset($conf['cookie']) ? $conf['cookie'] : false;
        if ($enabled) {
            $this->enabled = true;
            $this->cookies = & $_COOKIE;
        }
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->cookies[$this->prefix . $name];
        }
        return null;
    }

    /**
     * @param string $name
     * @param $val
     * @param int $expire expire seconds
     * @return $this
     */
    public function set($name, $val, $expire = 0)
    {
        if ($this->enabled) {
            setcookie($this->prefix . $name, $val, $this->calExpireSeconds($expire), $this->path, $this->domain);
        }
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function delete($name)
    {
        if ($this->has($name)) {
            $this->set($name, null);
        }
        return $this;
    }

    /**
     * Store for one year.
     *
     * @param string $name
     * @param $val
     */
    public function forever($name, $val)
    {
        if ($this->enabled) {
            $this->set($name, $val, 60 * 24 * 365);
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        if ($this->enabled) {
            return isset($this->cookies[$this->prefix . $name]);
        }
        return false;
    }

    /**
     * @param int $sec
     * @return int
     */
    private function calExpireSeconds($sec)
    {
        return ($sec > 0) ? time() + $sec : 0;
    }

}