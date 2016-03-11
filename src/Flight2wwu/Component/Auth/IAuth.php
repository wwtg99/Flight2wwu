<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/11
 * Time: 10:37
 */

namespace Flight2wwu\Component\Auth;


interface IAuth
{

    /**
     * @param array $user
     * @return bool
     */
    public function attempt(array $user);

    /**
     * @param array $user
     * @return mixed
     */
    public function login(array $user);

    /**
     * @return bool
     */
    public function isLogin();

    /**
     * @return mixed
     */
    public function logout();

    /**
     * @return array
     */
    public function getUser();

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
     * @return bool
     */
    public function isAccessed($path);

    /**
     * @return bool
     */
    public function isSuperuser();
}