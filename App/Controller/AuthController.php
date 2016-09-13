<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/22
 * Time: 15:53
 */

namespace Wwtg99\App\Controller;


use Wwtg99\App\Model\Auth\User;
use Wwtg99\App\Model\Message;
use Wwtg99\Flight2wwu\Common\BaseController;

class AuthController extends BaseController
{

    public static function login()
    {
        if (self::checkMethod('POST')) {
            self::postLogin();
        } else {
            self::getLogin();
        }
        return false;
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
        return false;
    }

    public static function info()
    {
        if (self::checkMethod('POST')) {
            AuthController::postInfo();
        } else {
            AuthController::getInfo();
        }
        return false;
    }

    public static function signup()
    {
        if (self::checkMethod('POST')) {
            AuthController::postSignup();
        } else {
            AuthController::getSignup();
        }
    }

    public static function user_edit()
    {
        if (self::checkMethod('POST')) {
            AuthController::postEdit();
        } else {
            AuthController::getEdit();
        }
    }

    private static function getLogin()
    {
        if (getAuth()->isLogin()) {
            \Flight::redirect(getConfig()->get('defined_routes.logout'));
        } else {
            getView()->render('auth/login', ['title'=>'Login']);
        }
    }

    private static function postLogin()
    {
        $name = self::getPost('username');
        $pwd = self::getPost('password');
        $rem = self::getPost('remember');
        getOValue()->addOld('username', $name);
        $redirectPath = '/';
        if (getAuth()->attempt([User::KEY_USER_NAME=>$name, User::KEY_USER_PASSWORD=>$pwd, 'remember'=>$rem])) {
            $path = getOValue()->getOldOnce('last_path');
            if ($path) {
                $redirectPath = $path;
            }
            \Flight::redirect($redirectPath);
        } else {
            $msg = Message::getMessage(21);
            getView()->render('auth/login', ['msg'=>$msg, 'title'=>'Login']);
        }
    }

    private static function getLogout()
    {
        getView()->render('auth/logout', ['user'=>getUser(), 'title'=>'Logout']);
    }

    private static function postLogout() {
        getAuth()->logout();
        $c = getConfig();
        $path = U($c->get('defined_routes.login'));
        \Flight::redirect($path);
    }

    private static function getChangePwd()
    {
        if (getAuth()->isLogin()) {
            getView()->render('auth/change_pwd', ['title'=>'Change Password']);
        } else {
            \Flight::redirect(getConfig()->get('defined_routes.login'));
        }
    }

    private static function postChangePwd()
    {
        $old = self::getPost('old');
        $new1 = self::getPost('new1');
        $new2 = self::getPost('new2');
        if (!$new1 || !$new2){
            $msg = Message::getMessage(15);
        } elseif ($new1 != $new2) {
            $msg = Message::getMessage(22);
        } else {
            $user = getAuth()->getUserObject();
            if ($user && $user->changePassword($old, $new1)) {
                $msg = Message::getMessage(23);
            } else {
                $msg = Message::getMessage(24);
            }
        }
        getView()->render('auth/change_pwd', ['msg'=>$msg, 'title'=>'Change Password']);
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

    private static function postSignup()
    {

    }

    private static function getEdit()
    {

    }

    private static function postEdit()
    {

    }
} 