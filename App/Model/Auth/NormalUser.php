<#php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/9/17 0017
 * Time: 下午 4:41
 */

namespace Wwtg99\App\Model\Auth;


use Wwtg99\Flight2wwu\Component\Auth\AuthUser;
use Wwtg99\PgAuth\Auth\IAuth;
use Wwtg99\PgAuth\Auth\NormalAuth;

class NormalUser extends AuthUser
{

    protected $originUser = [];

    /**
     * TODO
     * Verify user.
     *
     * @param array $user
     * @return bool
     */
    public function verify($user)
    {
        $this->originUser = $user;
        $auth = $this->getAuth();
        $u = $auth->verify($user);
        if ($u) {
            $this->user = $u->getUser();
            return true;
        }
        return false;
    }

    /**
     * TODO
     * Change password.
     *
     * @param $old
     * @param $new
     * @return bool
     */
    public function changePassword($old, $new)
    {
        $auth = $this->getAuth();
        $u = $auth->verify($this->user);
        if ($u) {
            return $u->changePassword($new);
        }
        return false;
    }

    /**
     * TODO
     * Change user info.
     *
     * @param array $user
     * @return bool
     */
    public function changeInfo($user)
    {
        $auth = $this->getAuth();
        $u = $auth->verify($this->user);
        if ($u) {
            return $u->changeInfo($user);
        }
        return false;
    }

    /**
     * Sign up new user.
     *
     * @param array $user
     * @return bool
     */
    public function signUp($user)
    {
        $auth = $this->getAuth();
        $this->originUser = $user;
        $u = $auth->signUp($user);
        if ($u) {
            $this->user = $u->getUser();
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        $r = isset($this->user[UserFactory::KEY_ROLES]) ? $this->user[UserFactory::KEY_ROLES] : [];
        return $r;
    }

    /**
     * @return mixed
     */
    public function login()
    {
        $auth = $this->getAuth();
        $u = $auth->signIn($this->originUser);
        if ($u) {
            $this->user = $u->getUser();
            return $u->getUser();
        }
        return $auth->getMessage();
    }

    /**
     * @return mixed
     */
    public function logout()
    {
        $auth = $this->getAuth();
        $u = $auth->signOut($this->user);
        if ($u) {
            $this->user = [];
            return $u->getUser();
        }
        return $auth->getMessage();
    }

    /**
     * @return IAuth
     */
    protected function getAuth()
    {
        $cache = getConfig()->get('auth_cache');
        $ttl = getConfig()->get('token_ttl');
        $auth = new NormalAuth(getDataPool()->getConnection('auth'), ['cache'=>$cache, 'token_ttl'=>$ttl]);
        return $auth;
    }


}