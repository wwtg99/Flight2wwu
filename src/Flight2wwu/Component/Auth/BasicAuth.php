<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/6
 * Time: 17:23
 */

namespace Flight2wwu\Component\Auth;

use Flight2wwu\Common\ServiceProvider;
use App\Model\User;

class BasicAuth implements ServiceProvider
{

    const SESSION_KEY = 'user';
    const COOKIE_TOKEN = 'USER_TOKEN';

    /**
     * @var array
     */
    private $user = [];

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

    function __construct()
    {

    }

    /**
     * Save user to session.
     *
     * @param array $user
     * @return boolean
     */
    public function login($user)
    {
        if (array_key_exists('user_id', $user) && array_key_exists('username', $user)) {
            $this->user = $user;
            $_SESSION[BasicAuth::SESSION_KEY] = $user;
            return true;
        }
        return false;
    }

    /**
     * Check login.
     *
     * @return bool
     */
    public function isLogin()
    {
        if ($this->user) {
            return true;
        }
        return $this->check();
    }

    /**
     * Clear user.
     */
    public function logout()
    {
        $this->user = null;
        unset($_SESSION[BasicAuth::SESSION_KEY]);
        setcookie(BasicAuth::COOKIE_TOKEN);
    }

    /**
     * Attempt to login.
     *
     * @param string $user
     * @return bool
     */
    public function attempt($user)
    {
        $u = User::verify($user);
        if ($u) {
            $this->login($u);
            setcookie(BasicAuth::COOKIE_TOKEN, $u['token']);
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getUser()
    {
        if (!$this->user) {
            if (isset($_SESSION[BasicAuth::SESSION_KEY])) {
                if (is_array($_SESSION[BasicAuth::SESSION_KEY])) {
                    $this->user = $_SESSION[BasicAuth::SESSION_KEY];
                }
            }
        }
        return $this->user;
    }

    /**
     * @param string $old
     * @param string $new
     * @return bool
     */
    public function changePwd($old, $new)
    {
        if ($this->isLogin()) {
            return User::changePassword($this->getUser()['user_id'], $old, $new);
        }
        return false;
    }

    /**
     * Check user in session or token
     *
     * @return bool
     */
    private function check() {
        if ($this->getUser()) {
            return true;
        }
        if (isset($_COOKIE[BasicAuth::COOKIE_TOKEN]) && $_COOKIE[BasicAuth::COOKIE_TOKEN]) {
            $token = $_COOKIE[BasicAuth::COOKIE_TOKEN];
            return $this->attempt(['token'=>$token]);
        }
        return false;
    }

}