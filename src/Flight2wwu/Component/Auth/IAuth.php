<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/11
 * Time: 10:37
 */

namespace Wwtg99\Flight2wwu\Component\Auth;


interface IAuth
{

    /**
     * Attempt to login.
     *
     * @param array $user
     * @return bool
     */
    public function attempt(array $user);

    /**
     * @return bool
     */
    public function isLogin();

    /**
     * Login user to storage (session or cookies).
     *
     * @param AuthUser $user
     * @param bool $writeCookies
     * @return IAuth
     */
    public function login($user, $writeCookies = true);

    /**
     * Logout User in storage (session or cookies).
     *
     * @return mixed
     */
    public function logout();

    /**
     * @return array
     */
    public function getUser();

    /**
     * @return AuthUser
     */
    public function getUserObject();

    /**
     * @param string|array $role
     * @return bool
     */
    public function hasRole($role);

    /**
     * @param string $object
     * @return AuthKey
     */
    public function getAuth($object);

    /**
     * @param string $path
     * @return AuthKey
     */
    public function accessPath($path);

    /**
     * @return bool
     */
    public function isSuperuser();
}