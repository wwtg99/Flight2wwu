<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/23
 * Time: 10:37
 */

namespace Wwtg99\Flight2wwu\Component\Auth;


use Wwtg99\App\Model\Auth\UserFactory;
use Wwtg99\PgAuth\Auth\IUser;

class RoleAuth implements IAuth
{

    const SESSION_KEY = 'user';
    const COOKIE_TOKEN_KEY = 'access_token';
    const COOKIE_USER_KEY = 'user_name';

    /**
     * @var IAuthUser
     */
    protected $user = null;

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
     * @var int
     */
    protected $sessionExpires = 60;

    /**
     * @var int
     */
    protected $cookieExpires = 600;

    /**
     * RoleAuth constructor.
     * @param array $conf
     */
    public function __construct($conf = [])
    {
        if (!$conf) {
            $conf = \Flight::get('config')->get('auth');
        }
        $this->loadConfig($conf);
    }

    /**
     * Attempt to login.
     *
     * @param array $user
     * @return bool
     */
    public function attempt(array $user)
    {
        if ($user) {
            $this->user = UserFactory::getUser();
            $re = $this->user->verify($user);
            if ($re) {
                $this->login($user);
                return true;
            }
        }
        return false;
    }

    /**
     * Login user to storage (session or cookies).
     *
     * @param array $user
     * @return IAuth
     */
    public function login(array $user)
    {
        if (!$this->user) {
            $this->user = UserFactory::getUser();
        }
        $re = $this->user->login($user);
        if ($re) {
            if ($this->use_session) {
                getSession()->set(self::SESSION_KEY, $this->user->getUser()->getUser(), $this->sessionExpires);
            }
            if ($this->use_cookie) {
                $u = $this->user->getUser()->getUser();
                if (isset($u[UserFactory::KEY_USER_TOKEN]) && isset($u[IUser::FIELD_USER_NAME])) {
                    $cookie_token = $u[UserFactory::KEY_USER_TOKEN];
                    $cookie_user = $u[IUser::FIELD_USER_NAME];
                    getCookie()->set(self::COOKIE_TOKEN_KEY, $cookie_token, $this->cookieExpires);
                    getCookie()->set(self::COOKIE_USER_KEY, $cookie_user, $this->cookieExpires);
                }
            }
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isLogin()
    {
        if ($this->user) {
            $u = $this->user->getUser();
            if ($u && isset($u->getUser()[IUser::FIELD_USER_ID])) {
                return true;
            }
        }
        //check session
        if ($this->use_session) {
            $user = getSession()->get(self::SESSION_KEY);
            if ($user) {
                $this->user = UserFactory::getUser($user);
                //refresh session
                getSession()->set(self::SESSION_KEY, $user, $this->sessionExpires);
                return true;
            }
        }
        //check cookies
        $uname = getCookie()->get(self::COOKIE_USER_KEY);
        $token = getCookie()->get(self::COOKIE_TOKEN_KEY);
        if ($uname && $token) {
            if (!$this->user) {
                $this->user = UserFactory::getUser();
            }
            $re = $this->user->verify([UserFactory::KEY_USER_NAME=>$uname, UserFactory::KEY_USER_TOKEN=>$token]);
            if ($re) {
                getSession()->set(self::SESSION_KEY, $this->user->getUser()->getUser(), $this->sessionExpires);
                return true;
            }
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function logout()
    {
        $this->user->logout();
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
     * @return IAuthUser
     */
    public function getUserObject()
    {
        if ($this->isLogin()) {
            return $this->user;
        }
        return null;
    }

    /**
     * @return array
     */
    public function getUser()
    {
        if ($this->isLogin()) {
            return $this->user->getUser()->getUser();
        }
        return [];
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
            return $this->user->isSuperuser();
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
     * @return array
     */
    private function getRoles()
    {
        $r = [];
        if ($this->user) {
            $r = $this->user->getRoles();
        }
        if (!$r) {
            $r = ['anonymous'];
        }
        return $r;
    }

    /**
     * @param array $arr
     */
    private function loadConfig(array $arr)
    {
        $this->use_session = isset($arr['session']) ? boolval($arr['session']) : false;
        $this->use_cookie = isset($arr['cookie']) ? boolval($arr['cookie']) : false;
        if (isset($arr['rbac']) && is_array($arr['rbac'])) {
            $this->rbac = $arr['rbac'];
        }
        if (isset($arr['session_expires'])) {
            $this->sessionExpires = intval($arr['session_expires']);
        }
        if (isset($arr['cookie_expires'])) {
            $this->cookieExpires = intval($arr['cookie_expires']);
        }
    }
} 