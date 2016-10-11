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
     * Check login.
     *
     * @return bool
     */
    public function isLogin();

    /**
     * Login user to storage (session or cookies).
     *
     * @param array $user
     * @return IAuth
     */
    public function login(array $user);

    /**
     * Logout User in storage (session or cookies).
     *
     * @return IAuth
     */
    public function logout();

    /**
     * @return array
     */
    public function getUser();

    /**
     * @return IAuthUser
     */
    public function getUserObject();

    /**
     * @param string|array $role
     * @return bool
     */
    public function hasRole($role);

    /**
     * Check object accessible.
     *
     * @param string $object
     * @return AuthKey
     */
    public function getAuth($object);

    /**
     * Check path accessible.
     *
     * @param string $path
     * @return AuthKey
     */
    public function accessPath($path);

}