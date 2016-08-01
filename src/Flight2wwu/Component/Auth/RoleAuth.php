<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/23
 * Time: 10:37
 */

namespace Wwtg99\Flight2wwu\Component\Auth;


use Wwtg99\App\Model\Auth\User;

class RoleAuth implements IAuth
{

    const SESSION_KEY = 'user';
    const COOKIE_TOKEN_KEY = 'access_token';
    const COOKIE_USER_KEY = 'user_name';

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
     * @var bool
     */
    protected $first = true;

    /**
     * @var int
     */
    protected $sessionExpires = 60;

    /**
     * @var int
     */
    protected $cookieExpires = 600;

    /**
     * RoleAuth constructor.
     */
    public function __construct()
    {
        $auth = \Flight::get('config')->get('auth');
        $this->loadConfig($auth);
    }

    /**
     * @inheritdoc
     */
    public function attempt(array $user = [])
    {
        if (!$user && $this->use_cookie) {
            //get from cookies
            $cookie_token = getCookie()->get(self::COOKIE_TOKEN_KEY);
            $cookie_user = getCookie()->get(self::COOKIE_USER_KEY);
            if ($cookie_user && $cookie_token) {
                $user[User::KEY_USER_TOKEN] = $cookie_token;
                $user[User::KEY_USER_NAME] = $cookie_user;
            }
        }
        $u = new User();
        $re = $u->verify($user);
        if ($re === true) {
            if ($this->first) {
                $this->login($u->getUser());
                $this->first = false;
            }
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function login(array $user)
    {
        $this->user = $user;
        if ($this->use_session) {
            getSession()->set(self::SESSION_KEY, $user, $this->sessionExpires);
        }
        if ($this->use_cookie) {
            if (isset($user[User::KEY_USER_TOKEN]) && isset($user[User::KEY_USER_NAME])) {
                $cookie_token = $user[User::KEY_USER_TOKEN];
                $cookie_user = $user[User::KEY_USER_NAME];
                getCookie()->set(self::COOKIE_TOKEN_KEY, $cookie_token, $this->cookieExpires);
                getCookie()->set(self::COOKIE_USER_KEY, $cookie_user, $this->cookieExpires);
            }
        }
    }

    /**
     * @return mixed
     */
    public function isLogin()
    {
        if ($this->getUser()) {
            return true;
        }
        return false;
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
            getCookie()->delete(self::COOKIE_TOKEN_KEY);
            getCookie()->delete(self::COOKIE_USER_KEY);
        }
    }

    /**
     * @return array
     */
    public function getUser()
    {
        if (!$this->user) {
            if ($this->use_session) {
                $this->user = getSession()->get(self::SESSION_KEY);
            }
            if (!$this->user) {
                $re = $this->attempt();
            }
        }
        return $this->user;
    }

    /**
     * @param array $arr
     */
    public function loadConfig(array $arr)
    {
        $this->use_session = isset($arr['session']) ? boolval($arr['session']) : false;
        $this->use_cookie = isset($arr['cookie']) ? boolval($arr['cookie']) : false;
        if (isset($arr['rbac']) && is_array($arr['rbac'])) {
            $this->rbac = $arr['rbac'];
        }
    }

    /**
     * @param string $object
     * @return AuthKey
     */
    public function getAuth($object)
    {
        $roles = $this->getRoles();
        return new AuthKey($this->getRoleAuth($roles, $object));
    }

    /**
     * @param string $path
     * @return AuthKey
     */
    public function accessPath($path)
    {
        $roles = $this->getRoles();
        return new AuthKey($this->getPathAuth($roles, $path));
    }

    /**
     * @param string|array $role_name
     * @return bool
     */
    public function hasRole($role_name)
    {
        if ($this->isLogin()) {
            $roles = $this->getRoles();
            if (!is_array($roles)) {
                $roles = [$roles];
            }
            if (is_array($role_name)) {
                return array_intersect($role_name, $roles) == $role_name;
            } else {
                return in_array($role_name, $roles);
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
            if (isset($this->user[User::KEY_SUPERUSER])) {
                return boolval($this->user[User::KEY_SUPERUSER]);
            }
        }
        return false;
    }

    /**
     * @param string|array $role
     * @param string $object
     * @return int
     */
    public function getRoleAuth($role, $object)
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
    public function getPathAuth($role, $path)
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

    /**
     * Add common_user to any login user
     *
     * @return array|string
     */
    private function getRoles()
    {
        if ($this->isLogin()) {
            $roles = isset($this->user[User::KEY_ROLES]) ? $this->user[User::KEY_ROLES] : [];
            if (is_array($roles)) {
                array_push($roles, 'common_user');
                return $roles;
            } else {
                $roles .= ',common_user';
                return explode(',', $roles);
            }
        }
        return 'anonymous';
    }
} 