<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/23
 * Time: 10:37
 */

namespace Flight2wwu\Component\Auth;

use App\Model\User;
use Flight2wwu\Common\ServiceProvider;

class RoleAuth implements ServiceProvider, IAuth
{

    const SESSION_KEY = 'user';
    const COOKIE_KEY = 'user';

    /**
     * @var array
     */
    protected $user = [];

    /**
     * @var bool
     */
    protected $use_session = true;

    /**
     * @var bool
     */
    protected $use_cookie = true;

    /**
     * @var array
     */
    protected $rbac = [];

    /**
     * @param array $user
     * @return bool
     */
    public function attempt(array $user)
    {
        if ($this->use_cookie) {
            $user['token'] = getCookie()->get(self::COOKIE_KEY);
        }
        $u = User::verify($user);
        if ($u) {
            $this->login($u);
            return true;
        }
        return false;
    }

    /**
     * @param array $user
     * @return mixed
     */
    public function login(array $user)
    {
        $this->user = $user;
        if ($this->use_session) {
            getSession()->set(self::SESSION_KEY, $user);
        }
        if ($this->use_cookie) {
            getCookie()->set(self::COOKIE_KEY, (isset($user['token']) ? $user['token'] : null));
        }
    }

    /**
     * @return mixed
     */
    public function isLogin()
    {
        if ($this->user) {
            return true;
        }
        return $this->attempt([]);
    }

    /**
     * @return mixed
     */
    public function logout()
    {
        $this->user = null;
        if ($this->use_session) {
            getSession()->delete(self::SESSION_KEY);
        }
        if ($this->use_cookie) {
            getCookie()->delete(self::COOKIE_KEY);
        }
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

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
        $auth = \Flight::get('auth');
        $this->use_session = isset($auth['session']) ? $auth['session'] : false;
        $this->use_cookie = isset($auth['cookie']) ? $auth['cookie'] : false;
        if ($auth && array_key_exists('rbac', $auth)) {
            $f = $auth['rbac'];
            if (file_exists($f)) {
                $c = require "$f";
                $this->loadRBAC($c);
            }
        }
    }

    /**
     * @param array $arr
     */
    public function loadRBAC(array $arr)
    {
        $this->rbac = $arr;
    }

    /**
     * @param string $object
     * @return AuthKey
     */
    public function getAuth($object)
    {
        if ($this->isLogin()) {
            $roles = $this->user['roles'];
        } else {
            $roles = 'anonymous';
        }
        return new AuthKey($this->getRoleAuth($roles, $object));
    }

    /**
     * @param string $path
     * @return bool
     */
    public function isAccessed($path)
    {
        if ($this->isLogin()) {
            $roles = $this->user['roles'];
        } else {
            $roles = 'anonymous';
        }
        return new AuthKey($this->getPathAuth($roles, $path));
    }

    /**
     * @param string|array $role_name
     * @return bool
     */
    public function hasRole($role_name)
    {
        if ($this->isLogin()) {
            $roles = $this->user['roles'];
            if (is_array($role_name)) {
                foreach ($role_name as $r) {
                    if (!array_key_exists($r, $roles)) {
                        return false;
                    }
                }
                return true;
            } else {
                return array_key_exists($role_name, $roles);
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isSuperuser()
    {
        if ($this->isLogin()) {
            if (isset($this->user['superuser'])) {
                return $this->user['superuser'];
            }
        }
        return false;
    }

    /**
     * @param string|array $role
     * @param string $object
     * @return int
     */
    private function getRoleAuth($role, $object)
    {
        $object = trim($object);
        if (is_array($role)) {
            $auth = -1;
            foreach ($role as $r) {
                $a = $this->getRoleAuth($r, $object);
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
     * @param string|array $role
     * @param string $path
     * @return int
     */
    private function getPathAuth($role, $path)
    {
        $path = trim($path);
        $a = $this->getRoleAuth($role, $path);
        if ($a === -1) {
            $spos = strrpos($path, '/');
            $spath = $path;
            while ($spos > 0) {
                $spath = substr($spath, 0, $spos);
                $p = $spath . '/*';
                $a = $this->getRoleAuth($role, $p);
                if ($a !== -1) {
                    break;
                } else {
                    $spos = strrpos($spath, '/');
                }
            }
            if ($a === -1) {
                $a = $this->getRoleAuth($role, '*');
            }
        }
        if ($a < 0) {
            $a = 0;
        }
        return $a;
    }
} 