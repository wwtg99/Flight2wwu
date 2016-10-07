<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/9/17 0017
 * Time: 下午 4:16
 */

namespace Wwtg99\Flight2wwu\Component\Auth;


abstract class AuthUser
{

    /**
     * @var array
     */
    protected $user = [];

    /**
     * AuthUser constructor.
     * @param array $user
     */
    public function __construct(array $user)
    {
        $this->user = $user;
    }

    /**
     * @return array
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
        return [];
    }

    /**
     * Verify user.
     *
     * @param array $user
     * @return bool
     */
    abstract public function verify($user);

    /**
     * Change password.
     *
     * @param $old
     * @param $new
     * @return bool
     */
    abstract public function changePassword($old, $new);

    /**
     * Change user info.
     *
     * @param array $user
     * @return bool
     */
    abstract public function changeInfo($user);

    /**
     * Sign up new user.
     *
     * @param array $user
     * @return bool
     */
    abstract public function signUp($user);

    /**
     * @return mixed
     */
    abstract public function login();

    /**
     * @return mixed
     */
    abstract public function logout();
}