<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/10
 * Time: 11:41
 */

namespace Flight2wwu\Component\Session;

use Flight2wwu\Common\ServiceProvider;

class LastValue implements ServiceProvider
{

    const OLD_KEY = 'LastValue_Old';
    const OLD_ONCE_KEY = 'LastValue_Old_Once';

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

    }

    /**
     * @param string $name
     * @param $val
     */
    public function addOld($name, $val)
    {
        if (!isset($_SESSION[LastValue::OLD_KEY])) {
            $_SESSION[LastValue::OLD_KEY] = [];
        }
        $_SESSION[LastValue::OLD_KEY][(string)$name] = $val;
    }

    /**
     * @param array $vals
     */
    public function addOlds(array $vals)
    {
        foreach ($vals as $name => $v) {
            $this->addOld($name, $v);
        }
    }

    /**
     * @param string $name
     * @param $val
     */
    public function addOldOnce($name, $val)
    {
        if (!isset($_SESSION[LastValue::OLD_ONCE_KEY])) {
            $_SESSION[LastValue::OLD_ONCE_KEY] = [];
        }
        $_SESSION[LastValue::OLD_ONCE_KEY][(string)$name] = $val;
    }

    /**
     * @param array $vals
     */
    public function addOldsOnce($vals)
    {
        foreach ($vals as $name => $v) {
            $this->addOldOnce($name, $v);
        }
    }

    /**
     * @param string $name
     * @param string $def
     * @return mixed
     */
    public function getOld($name, $def = '')
    {
        if (!isset($_SESSION[LastValue::OLD_KEY])) {
            return $def;
        }
        if (array_key_exists($name, $_SESSION[LastValue::OLD_KEY])) {
            return $_SESSION[LastValue::OLD_KEY][$name];
        }
        return $def;
    }

    /**
     * @return array
     */
    public function getOlds()
    {
        if (!isset($_SESSION[LastValue::OLD_KEY])) {
            return [];
        }
        return $_SESSION[LastValue::OLD_KEY];
    }

    /**
     * @param string $name
     * @param string $def
     * @return string
     */
    public function getOldOnce($name, $def = '')
    {
        if (!isset($_SESSION[LastValue::OLD_ONCE_KEY])) {
            return $def;
        }
        if (array_key_exists($name, $_SESSION[LastValue::OLD_ONCE_KEY])) {
            $v = $_SESSION[LastValue::OLD_ONCE_KEY][$name];
            unset($_SESSION[LastValue::OLD_ONCE_KEY][$name]);
            return $v;
        }
        return $def;
    }

    /**
     * @param $name
     */
    public function removeOld($name)
    {
        if (isset($_SESSION[LastValue::OLD_KEY])) {
            if (array_key_exists($name, $_SESSION[LastValue::OLD_KEY])) {
                unset($_SESSION[LastValue::OLD_KEY][$name]);
            }
        }
    }

    /**
     * Clear old value.
     */
    public function clearOld()
    {
        $_SESSION[LastValue::OLD_KEY] = [];
    }

    /**
     * Clear old once value.
     */
    public function clearOldOnce()
    {
        $_SESSION[LastValue::OLD_ONCE_KEY] = [];
    }
} 