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
        $this->enabled = isset($conf['session']) ? $conf['session'] : '';
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $_SESSION[$this->getSessionName($name)][0];
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
            $_SESSION[$this->getSessionName($name)] = [$val, $this->calExpireSeconds($expire)];
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
            $nm = $this->getSessionName($name);
            if (isset($_SESSION[$nm])) {
                $obj = $_SESSION[$nm];
                if (isset($obj[1]) && $obj[1] && $obj[1] < time()) {
                    unset($_SESSION[$nm]);
                } else {
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
        unset($_SESSION[$this->getSessionName($name)]);
        return $this;
    }

    /**
     * @param $name
     * @return string
     */
    public function getSessionName($name)
    {
        return $this->prefix . $name;
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