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
use Wwtg99\Flight2wwu\Component\Utils\FormatUtils;
use Wwtg99\PgAuth\Auth\IUser;

class AuthController extends BaseController
{


    /**
     * Login
     *
     * @return bool
     */
    public static function login()
    {
        if (self::checkMethod('POST')) {
            self::postLogin();
        } else {
            self::getLogin();
        }
        return false;
    }

    /**
     * Logout
     *
     * @return bool
     */
    public static function logout()
    {
        AuthController::getLogout();
        return false;
    }

    /**
     * Change password
     *
     * @return bool
     */
    public static function password()
    {
        if (self::checkMethod('POST')) {
            AuthController::postChangePwd();
        } else {
            AuthController::getChangePwd();
        }
        return false;
    }

    /**
     * Get user info
     *
     * @return bool
     */
    public static function info()
    {
        AuthController::getInfo();
        return false;
    }

    /**
     * Sign up
     *
     * @return bool
     */
    public static function signup()
    {
        if (self::checkMethod('POST')) {
            AuthController::postSignup();
        } else {
            AuthController::getSignup();
        }
        return false;
    }

    /**
     * Edit user info
     *
     * @return bool
     */
    public static function user_edit()
    {
        if (self::checkMethod('POST')) {
            AuthController::postEdit();
        } else {
            AuthController::getEdit();
        }
        return false;
    }

    /**
     * Forget password
     *
     * @return bool
     */
    public static function forget_password()
    {
        if (self::checkMethod('POST')) {
            AuthController::postForgetPassword();
        } else {
            AuthController::getForgetPassword();
        }
        return false;
    }

    /**
     * Change password after forgot
     *
     * @return bool
     */
    public static function forget_change_password()
    {
        if (self::checkMethod('POST')) {
            AuthController::postForgetChangePassword();
        } else {
            AuthController::getForgetChangePassword();
        }
        return false;
    }

    /**
     * Get new captcha
     *
     * @return bool
     */
    public static function update_captcha()
    {
        $builder = self::generateCaptcha();
        echo $builder->inline();
        return false;
    }



    private static function getLogin()
    {
        if (getAuth()->isLogin()) {
            $path = '/';
            \Flight::redirect($path);
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
        getAuth()->logout();
//        $c = getConfig();
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
        \Flight::json(FieldFormatter::formatDateTime($user), 200, true, 'utf8', JSON_UNESCAPED_UNICODE);
    }

    private static function getSignup()
    {
        if (getAuth()->isLogin()) {
            $path = '/';
            \Flight::redirect($path);
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
        } elseif (preg_match('/^[_\w]+$/', $name) < 1) {
            $msg = Message::getMessage(30);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.signup'));
            \Flight::redirect($rpath);
            return;
        }
        //check exists
        $u = getDataPool()->getConnection('auth')->getMapper('User');
        $uexist = $u->has([IUser::FIELD_USER_NAME=>$name]);
        $emailexist = $u->has([IUser::FIELD_EMAIL=>$email]);
        if ($uexist) {
            $msg = Message::getMessage(28);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.signup'));
            \Flight::redirect($rpath);
            return;
        } elseif ($emailexist) {
            $msg = Message::getMessage(29);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.signup'));
            \Flight::redirect($rpath);
            return;
        }
        $redirectPath = '/';
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
        $state = self::generateCSRFState();
        $u = getUser();
        getView()->render('auth/user_edit', ['user'=>FieldFormatter::formatDateTime($u), 'state'=>$state]);
    }

    private static function postEdit()
    {
        $label = self::getInput('label');
        $email = self::getInput('email');
        $des = self::getInput('descr');
        $state = self::getInput('state');
        $user = getAuth()->getUserObject();
        $d = [IUser::FIELD_LABEL=>$label, IUser::FIELD_EMAIL=>$email, 'descr'=>$des];
        //check email
        $u = getDataPool()->getConnection('auth')->getMapper('User');
        $emailexist = $u->has([IUser::FIELD_EMAIL=>$email]);
        if (!self::verifyCSRFState($state)) {
            $msg = Message::getMessage(25);
        } elseif ($emailexist) {
            $msg = Message::getMessage(29);
        } elseif ($user && $user->changeInfo($d)) {
            $msg = Message::getMessage(0, 'update successfully', 'success');
            getAuth()->refreshSession();
        } else {
            $msg = Message::getMessage(13);
        }
        getOValue()->addOldOnce('msg', $msg);
        $rpath = U(getConfig()->get('defined_routes.user_edit'));
        \Flight::redirect($rpath);
    }

    private static function getForgetPassword()
    {
        if (getAuth()->isLogin()) {
            $path = '/';
            \Flight::redirect($path);
        } else {
            $state = self::generateCSRFState();
            $builder = self::generateCaptcha();
            getView()->render('auth/forget_pwd', ['title' => 'Forget Password', 'state' => $state, 'captcha' => $builder]);
        }
    }

    private static function postForgetPassword()
    {
        $uname = self::getPost('username');
        $email = self::getPost('email');
        $state = self::getInput('state');
        $phrase = self::getInput('captcha');
        if (!self::verifyCSRFState($state)) {
            $msg = Message::getMessage(25);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.forget_password'));
            \Flight::redirect($rpath);
            return;
        } elseif (!self::verifyCaptcha($phrase)) {
            $msg = Message::getMessage(27);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.forget_password'));
            \Flight::redirect($rpath);
            return;
        } elseif (!$uname || !$email){
            $msg = Message::getMessage(15);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.forget_password'));
            \Flight::redirect($rpath);
            return;
        }
        $send = 0;
        //check
        $umodel = getDataPool()->getConnection('auth')->getMapper('User');
        $re = $umodel->get(null, IUser::FIELD_USER_ID, ['AND'=>[IUser::FIELD_USER_NAME=>$uname, IUser::FIELD_EMAIL=>$email]]);
        if ($re) {
            $token = md5($uname . ':' . $email . ':' . FormatUtils::randStr(20) . time());
            $token_ttl = 86400;
            getCache()->set($token, ['name'=>$uname, 'email'=>$email], $token_ttl);
            //send email
            $domain = '';
            $sub = '请及时修改您的密码';
            $body = "<p>请点击如下链接修改您的密码，如果无法打开，请复制链接在浏览器地址栏中。</p><p><a href='$domain/auth/forget_change_password?token=$token'>$domain/auth/forget_change_password?token=$token</a></p>";//TODO
            $mail = getMailer();
            $mail->send(['subject'=>$sub, 'to'=>$email, 'from'=>'from@email.com', 'body'=>$body]);//TODO
            $send = 1;
        }
        getView()->render('auth/forget_pwd', ['title'=>'Forget Password', 'email'=>$email, 'send'=>$send]);
    }

    private static function getForgetChangePassword()
    {
        $token = self::getInput('token');
        if ($token && getCache()->has($token)) {
            $u = getCache()->get($token);
            $state = self::generateCSRFState();
            $builder = self::generateCaptcha();
            getView()->render('auth/forget_change_pwd', ['title' => 'Reset Password', 'name' => $u['name'], 'state'=>$state, 'captcha'=>$builder, 'token'=>$token]);
        } else {
            \Flight::redirect(U('404'));
        }
    }

    private static function postForgetChangePassword()
    {
        $token = self::getInput('token');
        if ($token && getCache()->has($token)) {
            $state = self::getInput('state');
            $phrase = self::getInput('captcha');
            $pwd1 = self::getInput('pwd1');
            $pwd2 = self::getInput('pwd2');
            if (!self::verifyCSRFState($state)) {
                $msg = Message::getMessage(25);
                getOValue()->addOldOnce('msg', $msg);
                $rpath = self::getRequest()->url;
                \Flight::redirect($rpath);
                return;
            } elseif (!self::verifyCaptcha($phrase)) {
                $msg = Message::getMessage(27);
                getOValue()->addOldOnce('msg', $msg);
                $rpath = self::getRequest()->url;
                \Flight::redirect($rpath);
                return;
            } elseif ($pwd1 != $pwd2) {
                $msg = Message::getMessage(22);
                getOValue()->addOldOnce('msg', $msg);
                $rpath = self::getRequest()->url;
                \Flight::redirect($rpath);
                return;
            } else {
                $u = getCache()->get($token);
                getCache()->delete($token);
                $uname = $u['name'];
                $email = $u['email'];
                $umodel = getDataPool()->getConnection('auth')->getMapper('User');
                $re = $umodel->get(null, IUser::FIELD_USER_ID, ['AND'=>[IUser::FIELD_USER_NAME=>$uname, IUser::FIELD_EMAIL=>$email]]);
                if ($re) {
                    $re = $umodel->update([IUser::FIELD_PASSWORD=>password_hash($pwd1, PASSWORD_BCRYPT)], ['AND'=>[IUser::FIELD_USER_NAME=>$uname, IUser::FIELD_EMAIL=>$email]]);
                    if ($re) {
                        $msg = Message::getMessage(23);
                    } else {
                        $msg = Message::getMessage(24);
                    }
                } else {
                    $msg = Message::getMessage(11);
                }
            }
            getView()->render('auth/forget_change_pwd', ['title' => 'Reset Password', 'name' => $uname, 'msg'=>$msg]);
        } else {
            \Flight::redirect(U('404'));
        }
    }
} 