<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/11
 * Time: 11:20
 */

namespace Wwtg99\Flight2wwu\Component\Storage;


class SessionUtil implements IAttribute
{

    /**
     * @var bool
     */
    private $enabled = false;

    /**
     * @var array
     */
    private $session = [];

    /**
     * @var string
     */
    private $prefix;

    /**
     * SessionUtil constructor.
     * @param array $conf
     */
    public function __construct($conf = [])
    {
        if (!$conf) {
            $conf = \Flight::get('config')->get('storage');
        }
        $this->prefix = isset($conf['prefix']) ? $conf['prefix'] . '_' : '';
        $enabled = isset($conf['session']) ? $conf['session'] : '';
        if ($enabled) {
            $this->enabled = true;
            $this->start();
            $this->session = & $_SESSION;
        }
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->session[$this->prefix . $name][0];
        }
        return null;
    }

    /**
     * @param string $name
     * @param $val
     * @param int $expire
     * @return $this
     */
    public function set($name, $val, $expire = 0)
    {
        if ($this->enabled) {
            $this->session[$this->prefix . $name] = [$val, $this->calExpireSeconds($expire)];
        }
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        if ($this->enabled) {
            $name = $this->prefix . $name;
            if (isset($this->session[$name])) {
                if (!$this->session[$name][1] || time() <= $this->session[$name][1]) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function delete($name)
    {
        if ($this->has($name)) {
            unset($this->session[$this->prefix . $name]);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function start()
    {
        session_start();
        return $this;
    }

    /**
     * @return $this
     */
    public function destroy()
    {
        session_destroy();
        return $this;
    }

    /**
     * @return $this
     */
    public function close()
    {
        session_write_close();
        return $this;
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