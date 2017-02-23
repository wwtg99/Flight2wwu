<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2017/1/12
 * Time: 10:04
 */

namespace Wwtg99\Flight2wwu\Component\Auth;


use Wwtg99\App\Model\Auth\UserFactory;
use Wwtg99\PgAuth\Auth\IAuth;
use Wwtg99\PgAuth\Auth\IUser;
use Wwtg99\PgAuth\Auth\NormalUser;

class RBACAuth
{

    /**
     * @var bool
     */
    protected $useCookie = true;

    /**
     * @var array
     */
    protected $rbac = [];

    /**
     * @var int
     */
    protected $cookieExpires = 600;

    /**
     * @var string
     */
    protected $cookieTokenKey = 'access_token';

    /**
     * @var string
     */
    protected $cookieUserKey = 'user_name';

    /**
     * @var IAuth
     */
    protected $auth = null;

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
     * Save user to cookie
     */
    public function save()
    {
        if ($this->useCookie) {
            $this->saveCookie();
        }
    }

    /**
     * Delete user in cookie
     */
    public function deleteCookie()
    {
        if ($this->useCookie) {
            getCookie()->delete($this->cookieUserKey);
            getCookie()->delete($this->cookieTokenKey);
        }
    }

    /**
     * Save user to cookie
     */
    public function saveCookie()
    {
        if ($this->auth->getUser()) {
            $u = $this->auth->getUser()->getUserArray();
            if (isset($u[IUser::FIELD_TOKEN]) && isset($u[IUser::FIELD_USER_NAME])) {
                $cookie_token = $u[IUser::FIELD_TOKEN];
                $cookie_user = $u[IUser::FIELD_USER_NAME];
                getCookie()->set($this->cookieTokenKey, $cookie_token, $this->cookieExpires);
                getCookie()->set($this->cookieUserKey, $cookie_user, $this->cookieExpires);
            }
        }
    }

    /**
     * @return bool
     */
    public function isLogin()
    {
        if ($this->auth->getUser() && $this->auth->getUser()->getId()) {
            return true;
        }
        //check cookies
        if ($this->useCookie) {
            $uname = getCookie()->get($this->cookieUserKey);
            $token = getCookie()->get($this->cookieTokenKey);
            if ($uname && $token && $this->auth->verify([IAuth::KEY_USERNAME => $uname, IAuth::KEY_TOKEN => $token])) {
                return true;
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
            $u = $this->getAuth()->getUser()->getUserArray();
            $su = isset($u[IUser::FIELD_SUPERUSER]) ? $u[IUser::FIELD_SUPERUSER] : false;
            if ($su) {
                return true;
            }
            $r = $this->getRoles();
            if (in_array('admin', $r)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $user
     * @return null|IUser
     */
    public function signup($user)
    {
        $u = $this->auth->signUp($user);
        $this->save();
        return $u;
    }

    /**
     * @param $user
     * @return bool
     */
    public function verify($user)
    {
        return $this->auth->verify($user);
    }

    /**
     * @param $user
     * @return null|IUser
     */
    public function login($user)
    {
        $u = $this->auth->signIn($user);
        $this->save();
        return $u;
    }

    /**
     * Logout
     */
    public function logout()
    {
        if ($this->auth->getUser()) {
            $this->auth->signOut($this->auth->getUser()->getUserArray());
        }
        $this->deleteCookie();
    }

    /**
     * @return IUser
     */
    public function getUser()
    {
        if ($this->isLogin()) {
            return $this->auth->getUser();
        }
        return null;
    }

    /**
     * @return IAuth
     */
    public function getAuth()
    {
        return $this->auth;
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
     * @return boolean
     */
    public function isUseCookie()
    {
        return $this->useCookie;
    }

    /**
     * @param boolean $use_cookie
     * @return RBACAuth
     */
    public function setUseCookie($use_cookie)
    {
        $this->useCookie = $use_cookie;
        return $this;
    }

    /**
     * @return int
     */
    public function getCookieExpires()
    {
        return $this->cookieExpires;
    }

    /**
     * @param int $cookieExpires
     * @return RBACAuth
     */
    public function setCookieExpires($cookieExpires)
    {
        $this->cookieExpires = $cookieExpires;
        return $this;
    }

    /**
     * @return array
     */
    private function getRoles()
    {
        $r = [];
        $u = $this->auth->getUser();
        if ($u) {
            if ($u instanceof NormalUser) {
                $r = $u->getRoles();
            }
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
        $this->useCookie = isset($arr['cookie']) ? boolval($arr['cookie']) : false;
        if (isset($arr['rbac']) && is_array($arr['rbac'])) {
            $this->rbac = $arr['rbac'];
        }
        if (isset($arr['cookie_expires'])) {
            $this->cookieExpires = intval($arr['cookie_expires']);
        }
        if (isset($arr['cookie_token_key'])) {
            $this->cookieTokenKey = $arr['cookie_token_key'];
        }
        if (isset($arr['cookie_user_key'])) {
            $this->cookieUserKey = $arr['cookie_user_key'];
        }
        $this->auth = UserFactory::getAuth($arr);
    }
}