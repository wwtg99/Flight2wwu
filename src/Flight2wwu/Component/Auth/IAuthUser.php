<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/10/9
 * Time: 18:14
 */

namespace Wwtg99\Flight2wwu\Component\Auth;


interface IAuthUser
{

    /**
     * @return mixed
     */
    public function getUser();

    /**
     * @return array
     */
    public function getRoles();

    /**
     * @param array $user
     * @return bool
     */
    public function verify(array $user);

    /**
     * @param $old
     * @param $new
     * @return bool
     */
    public function changePassword($old, $new);

    /**
     * @param array $user
     * @return bool
     */
    public function changeInfo(array $user);

    /**
     * @param array $user
     * @return bool
     */
    public function signUp(array $user);

    /**
     * @param array $user
     * @return bool
     */
    public function login(array $user);

    /**
     * @return bool
     */
    public function logout();

    /**
     * @return bool
     */
    public function isSuperuser();

    /**
     * @return mixed
     */
    public function getMessage();
}