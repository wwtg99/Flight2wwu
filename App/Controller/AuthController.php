<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/22
 * Time: 15:53
 */

namespace Wwtg99\App\Controller;


use Wwtg99\App\Model\Auth\UserFactory;
use Wwtg99\App\Model\Message;
use Wwtg99\DataPool\Utils\FieldFormatter;
use Wwtg99\Flight2wwu\Common\BaseController;
use Wwtg99\PgAuth\Auth\IUser;

class AuthController extends BaseController
{

    const PHRASE_EXPIRES = 600;

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
            \Flight::redirect(U(getConfig()->get('defined_routes.logout')));
        } else {
            $state = self::generateCSRFState();
            $builder = self::generateCaptcha();
            getView()->render('auth/login', ['title'=>'Login', 'state'=>$state, 'captcha'=>$builder]);
        }
    }

    private static function postLogin()
    {
        $name = self::getPost('username');
        $pwd = self::getPost('password');
        $rem = self::getPost('remember');
        getOValue()->addOld('login_username', $name);
        $state = self::getInput('state');
        $phrase = self::getInput('captcha');
        if (!self::verifyCSRFState($state)) {
            $msg = Message::getMessage(25);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.login'));
            \Flight::redirect($rpath);
            return;
        } elseif (!self::verifyCaptcha($phrase)) {
            $msg = Message::getMessage(27);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.login'));
            \Flight::redirect($rpath);
            return;
        }
        $redirectPath = '/';
        if (getAuth()->attempt([UserFactory::KEY_USER_NAME=>$name, UserFactory::KEY_USER_PASSWORD=>$pwd, 'remember'=>$rem])) {
            $path = getOValue()->getOldOnce('last_path');
            if ($path) {
                $redirectPath = $path;
            }
            \Flight::redirect($redirectPath);
        } else {
            $msg = Message::getMessage(21);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.login'));
            \Flight::redirect($rpath);
        }
    }

    private static function getLogout()
    {
        getView()->render('auth/logout', ['user'=>getUser(), 'title'=>'Logout']);
    }

    private static function postLogout() {
        getAuth()->logout();
        $c = getConfig();
        $path = '/';
//        $path = U($c->get('defined_routes.login'));
        \Flight::redirect($path);
    }

    private static function getChangePwd()
    {
        if (getAuth()->isLogin()) {
            $state = self::generateCSRFState();
            getView()->render('auth/change_pwd', ['title'=>'Change Password', 'state'=>$state]);
        } else {
            \Flight::redirect(U(getConfig()->get('defined_routes.login')));
        }
    }

    private static function postChangePwd()
    {
        $old = self::getPost('old');
        $new1 = self::getPost('new1');
        $new2 = self::getPost('new2');
        $state = self::getInput('state');
        if (!self::verifyCSRFState($state)) {
            $msg = Message::getMessage(25);
        } elseif (!$new1 || !$new2){
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
        getOValue()->addOldOnce('msg', $msg);
        $rpath = U(getConfig()->get('defined_routes.change_password'));
        \Flight::redirect($rpath);
    }

    private static function getInfo()
    {
        $user = getUser();
        getView()->render('auth/user_info', ['user'=>FieldFormatter::formatFields($user, ['format_datetime'=>[], 'format_number'=>[]]), 'title'=>'User Center']);
    }

    private static function postInfo()
    {

    }

    private static function getSignup()
    {
        if (getAuth()->isLogin()) {
            \Flight::redirect(U(getConfig()->get('defined_routes.logout')));
        } else {
            $state = self::generateCSRFState();
            $builder = self::generateCaptcha();
            getView()->render('auth/signup', ['title'=>'Sign Up', 'state'=>$state, 'captcha'=>$builder]);
        }
    }

    private static function postSignup()
    {
        $name = self::getPost('username');
        $email = self::getPost('email');
        $pwd = self::getPost('password');
        $pwd2 = self::getPost('password2');
        $state = self::getInput('state');
        $phrase = self::getInput('captcha');
        getOValue()->addOld('signup_username', $name);
        getOValue()->addOld('signup_email', $email);
        if (!self::verifyCSRFState($state)) {
            $msg = Message::getMessage(25);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.signup'));
            \Flight::redirect($rpath);
            return;
        } elseif (!self::verifyCaptcha($phrase)) {
            $msg = Message::getMessage(27);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.signup'));
            \Flight::redirect($rpath);
            return;
        } elseif ($pwd != $pwd2) {
            $msg = Message::getMessage(22);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.signup'));
            \Flight::redirect($rpath);
            return;
        }
        $redirectPath = U(getConfig()->get('defined_routes.user_home'));
        $user = UserFactory::getUser();
        $u = [UserFactory::KEY_USER_NAME=>$name, UserFactory::KEY_USER_PASSWORD=>$pwd, UserFactory::KEY_USER_EMAIL=>$email];
        if ($user->signUp($u)) {
            getAuth()->login($u);
            \Flight::redirect($redirectPath);
        } else {
            $msg = Message::getMessage(26);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.signup'));
            \Flight::redirect($rpath);
        }
    }

    private static function getEdit()
    {
        $u = getUser();
        getView()->render('auth/user_edit', ['user'=>FieldFormatter::formatDateTime($u)]);
    }

    private static function postEdit()
    {
        $label = self::getInput('label');
        $email = self::getInput('email');
        $des = self::getInput('descr');
        $user = getAuth()->getUserObject();
        $d = [IUser::FIELD_LABEL=>$label, IUser::FIELD_EMAIL=>$email, 'descr'=>$des];
        if ($user && $user->changeInfo($d)) {
            $msg = Message::getMessage(0, 'update successfully', 'success');
            getAuth()->refreshSession();
        } else {
            $msg = Message::getMessage(13);
        }
        getOValue()->addOldOnce('msg', $msg);
        \Flight::redirect('/auth/user_edit');
    }
} 