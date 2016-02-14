<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/22
 * Time: 15:53
 */

namespace App\Controller;

use Flight2wwu\Common\BaseController;

class AuthController extends BaseController
{

    /**
     * Path to login.
     *
     * @var string
     */
    public static $loginPath = '/auth/login';

    /**
     * Path to logout.
     *
     * @var string
     */
    public static $logoutPath = '/auth/logout';

    /**
     * Path to change password.
     *
     * @var string
     */
    public static $pwdPath = '/auth/password';

    /**
     * Path to redirect after login.
     *
     * @var string
     */
    public static $redirectPath = '/';

    public static function login()
    {
        if (self::checkMethod('POST')) {
            AuthController::postLogin();
        } else {
            AuthController::getLogin();
        }
    }

    public static function logout()
    {
        if (self::checkMethod('POST')) {
            AuthController::postLogout();
        } else {
            AuthController::getLogout();
        }
    }

    public static function password()
    {
        if (self::checkMethod('POST')) {
            AuthController::postChangePwd();
        } else {
            AuthController::getChangePwd();
        }
    }

    public static function info()
    {
        if (self::checkMethod('POST')) {
            AuthController::postInfo();
        } else {
            AuthController::getInfo();
        }
    }

    public static function signup()
    {
        if (self::checkMethod('POST')) {
            AuthController::postSignup();
        } else {
            AuthController::getSignup();
        }
    }

    private static function getlogin()
    {
        if (getAuth()->isLogin()) {
            \Flight::redirect(self::$logoutPath);
        } else {
            getView()->render('auth/login');
        }
    }

    private static function postLogin()
    {
        $name = self::getPost('name');
        $pwd = self::getPost('password');
        $rem = self::getPost('remember');
        getLValue()->addOld('username', $name);
        if (getAuth()->attempt(['username'=>$name, 'password'=>$pwd, 'remember'=>$rem])) {
            \Flight::redirect(self::$redirectPath);
        } else {
            getLValue()->addOldOnce('login_error', 'login failed');
            \Flight::redirect(self::$loginPath);
        }
    }

    private static function getLogout()
    {
        getView()->render('auth/logout', getUser());
    }

    private static function postLogout() {
        getAuth()->logout();
        \Flight::redirect(self::$redirectPath);
    }

    private static function getChangePwd()
    {
        if (getAuth()->isLogin()) {
            getView()->render('auth/change_pwd');
        } else {
            \Flight::redirect(self::$loginPath);
        }
    }

    private static function postChangePwd()
    {
        $old = self::getPost('old');
        $new1 = self::getPost('new1');
        $new2 = self::getPost('new2');
        if ($new1 != $new2) {
            getLValue()->addOldOnce('auth_error', 'password mismatch');
            \Flight::redirect(self::$pwdPath);
        } else {
            if (getAuth()->changePwd($old, $new1)) {
                getLValue()->addOldOnce('auth_error', 'password changed');
            } else {
                getLValue()->addOldOnce('auth_error', 'password not changed');
            }
            \Flight::redirect(self::$pwdPath);
        }
    }

    private static function getInfo()
    {

    }

    private static function postInfo()
    {

    }

    private static function getSignup()
    {

    }

    public static function postSignup()
    {

    }
} 