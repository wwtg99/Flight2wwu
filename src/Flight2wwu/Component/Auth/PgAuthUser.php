<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/10/9
 * Time: 18:18
 */

namespace Wwtg99\Flight2wwu\Component\Auth;


use Wwtg99\App\Model\Auth\UserFactory;
use Wwtg99\PgAuth\Auth\IAuth as A;
use Wwtg99\PgAuth\Auth\IUser;

/**
 * @Deprecated
 * Class PgAuthUser
 * Wrapper for Wwtg99\PgAuth\Auth\IUser
 * @package Wwtg99\Flight2wwu\Component\Auth
 */
abstract class PgAuthUser implements IAuthUser
{

    /**
     * @var IUser
     */
    protected $user = null;

    /**
     * @var A
     */
    protected $auth = null;

    /**
     * PgAuthUser constructor.
     * @param $user
     */
    public function __construct($user)
    {
        if ($user instanceof IUser) {
            $this->user = $user;
        }
    }

    /**
     * @return IUser
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        if ($this->user) {
            $u = $this->user->getUser();
            $r = isset($u[IUser::FIELD_ROLES]) ? $u[IUser::FIELD_ROLES] : [];
            if (!is_array($r)) {
                $r = explode(',', $r);
            }
            if (!in_array('common_user', $r)) {
                array_push($r, 'common_user');
            }
            return $r;
        } else {
            return [];
        }
    }

    /**
     * @param array $user
     * @return bool
     */
    public function verify(array $user)
    {
        if ($this->auth) {
            $u = $this->auth->verify($user);
            if ($u) {
                $this->user = $u;
                return true;
            }
        }
        return false;
    }

    /**
     * @param $old
     * @param $new
     * @return bool
     */
    public function changePassword($old, $new)
    {
        if ($this->auth) {
            if ($this->user) {
                $u = [IUser::FIELD_USER_NAME => $this->user->getUser()[IUser::FIELD_USER_NAME], UserFactory::KEY_USER_PASSWORD => $old];
                $re = $this->auth->verify($u);
                if ($re) {
                    return $this->user->changePassword($new);
                }
            }
        }
        return false;
    }

    /**
     * @param array $user
     * @return bool
     */
    public function changeInfo(array $user)
    {
        if ($this->user) {
            return $this->user->changeInfo($user);
        }
        return false;
    }

    /**
     * @param array $user
     * @return bool
     */
    public function signUp(array $user)
    {
        if ($this->auth) {
            $this->user = $this->auth->signUp($user);
            if ($this->user) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $user
     * @return bool
     */
    public function login(array $user)
    {
        if ($this->auth) {
            $this->user = $this->auth->signIn($user);
            if ($this->user) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function logout()
    {
        if ($this->auth) {
            $u = $this->auth->signOut($this->user->getUser());
            if ($u) {
                $this->user = null;
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
        $u = $this->user ? $this->user->getUser() : [];
        $su = isset($u[UserFactory::KEY_SUPERUSER]) ? $u[UserFactory::KEY_SUPERUSER] : false;
        if (!$su) {
            $r = $this->getRoles();
            return array_key_exists('admin', $r);
        }
        return $su;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        if ($this->auth) {
            return $this->auth->getMessage();
        }
        return '';
    }


}