<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/16
 * Time: 11:40
 */

namespace Flight2wwu\Component\Auth;


use Flight2wwu\Common\ServiceProvider;

class RoleBasedAccessControl implements ServiceProvider
{
    /**
     * @var array
     */
    private $rbac = [];

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
        $conf = \Flight::get('auth');
        if ($conf && array_key_exists('rbac', $conf)) {
            $f = $conf['rbac'];
            if (file_exists($f)) {
                $c = require "$f";
                $this->loadConfig($c);
            }
        }
    }

    /**
     * @param array $arr
     */
    public function loadConfig(array $arr)
    {
        $this->rbac = $arr;
    }

    /**
     * @param array|string $role
     * @param string $object
     * @return int
     */
    public function getAuth($role, $object)
    {
        $object = trim($object);
        if (is_array($role)) {
            $auth = -1;
            foreach ($role as $r) {
                $a = $this->getAuth($r, $object);
                if ($a >= 0) {
                    if ($auth == -1) {
                        $auth = 0;
                    }
                    $auth |= $a;
                }
            }
            return $auth;
        } else {
            if (array_key_exists($role, $this->rbac)) {
                $ra = $this->rbac[$role];
                if (array_key_exists($object, $ra)) {
                    return $ra[$object];
                }
            }
        }
        return -1;
    }

    /**
     * @param array|string $role
     * @param string $path
     * @return int
     */
    public function getPathAuth($role, $path)
    {
        $path = trim($path);
        $a = $this->getAuth($role, $path);
        if ($a === -1) {
            $spos = strrpos($path, '/');
            $spath = $path;
            while ($spos > 0) {
                $spath = substr($spath, 0, $spos);
                $p = $spath . '/*';
                $a = $this->getAuth($role, $p);
                if ($a !== -1) {
                    break;
                } else {
                    $spos = strrpos($spath, '/');
                }
            }
            if ($a === -1) {
                $a = $this->getAuth($role, '*');
            }
        }
        return $a;
    }

} 