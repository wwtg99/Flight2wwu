<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/11
 * Time: 11:20
 */

namespace Flight2wwu\Component\Storage;


use Flight2wwu\Common\ServiceProvider;

class SessionUtil implements ServiceProvider, IAttribute
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
     * @return mixed
     */
    public function register()
    {

    }

    /**
     * @return mixed
     */
    public function boot()
    {
        $st = \Flight::get('storage');
        $enabled = array_key_exists('session', $st) ? $st['session'] : '';
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
            return $this->session[$name];
        }
        return null;
    }

    /**
     * @param string $name
     * @param $val
     * @return $this
     */
    public function set($name, $val)
    {
        if ($this->enabled) {
            $this->session[$name] = $val;
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
            return isset($this->session[$name]);
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
            unset($this->session[$name]);
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

}