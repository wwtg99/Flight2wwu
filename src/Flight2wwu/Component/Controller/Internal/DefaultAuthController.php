<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2017/2/5
 * Time: 14:16
 */

namespace Wwtg99\Flight2wwu\Component\Controller\Internal;


use Wwtg99\App\Controller\DefaultController;
use Wwtg99\App\Model\Message;
use Wwtg99\Flight2wwu\Component\Controller\BaseController;
use Wwtg99\Flight2wwu\Component\Utils\CSRFCode;
use Wwtg99\Flight2wwu\Component\Utils\FormatUtils;
use Wwtg99\PgAuth\Auth\IAuth;
use Wwtg99\PgAuth\Auth\IUser;

class DefaultAuthController extends BaseController
{

    public static $forgetEmailSubject = '请及时修改您的密码';

    /**
     * Replace $domain$ with domain, $token$ with token
     * @var string
     */
    public static $forgetEmailBody = '<p>请点击如下链接修改您的密码，如果无法打开，请复制链接在浏览器地址栏中。</p><p><a href="$domain$/auth/forget_change_password?token=$token$">$domain/auth/forget_change_password?token=$token$</a></p>';

    public static $formatEmailFrom = ['test@email.com'=>'no-reply'];

    /**
     * Login
     *
     * @return bool
     */
    public static function login()
    {
        if (self::getRequest()->checkMethod('POST')) {
            return self::postLogin();
        } else {
            return self::getLogin();
        }
    }

    /**
     * Logout
     *
     * @return bool
     */
    public static function logout()
    {
        return self::getLogout();
    }

    /**
     * Change password
     *
     * @return bool
     */
    public static function password()
    {
        if (self::getRequest()->checkMethod('POST')) {
            return self::postChangePwd();
        } else {
            return self::getChangePwd();
        }
    }

    /**
     * Sign up
     *
     * @return bool
     */
    public static function signup()
    {
        if (self::getRequest()->checkMethod('POST')) {
            return self::postSignup();
        } else {
            return self::getSignup();
        }
    }

    /**
     * Forget password
     *
     * @return bool
     */
    public static function forget_password()
    {
        if (self::getRequest()->checkMethod('POST')) {
            return self::postForgetPassword();
        } else {
            return self::getForgetPassword();
        }
    }

    /**
     * Change password after forgot
     *
     * @return bool
     */
    public static function forget_change_password()
    {
        if (self::getRequest()->checkMethod('POST')) {
            return self::postForgetChangePassword();
        } else {
            return self::getForgetChangePassword();
        }
    }

    /**
     * Get new captcha
     *
     * @return bool
     */
    public static function update_captcha()
    {
        $builder = getCaptcha()->generateCaptcha();
        echo $builder->inline();
        return false;
    }



    private static function getLogin()
    {
        if (getAuth()->isLogin()) {
            $path = '/';
            \Flight::redirect($path);
        } else {
            $state = getCSRF()->generateCSRFCode();
            $builder = getCaptcha()->generateCaptcha();
            self::getResponse()
                ->setHeader(DefaultController::$defaultViewHeaders)
                ->setResType('view')
                ->setView('auth/login')
                ->setData(['title'=>'Login', CSRFCode::$key=>$state, 'captcha'=>$builder])
                ->send();
        }
        return false;
    }

    private static function postLogin()
    {
        $req = self::getRequest();
        $name = $req->getPost('username');
        $pwd = $req->getPost('password');
        getOValue()->addOld('login_username', $name);
        $phrase = $req->getInput('captcha');
        if (!CSRFCode::check()) {
            $msg = Message::getMessage(25);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.login'));
            \Flight::redirect($rpath);
            return false;
        } elseif (!getCaptcha()->verifyCaptcha($phrase)) {
            $msg = Message::getMessage(27);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.login'));
            \Flight::redirect($rpath);
            return false;
        }
        $redirectPath = '/';
        $u = [IAuth::KEY_USERNAME=>$name, IAuth::KEY_PASSWORD=>$pwd];
        $user = getAuth()->login($u);
        if ($user) {
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
        return false;
    }

    private static function getLogout()
    {
        getAuth()->logout();
        $path = '/';
        \Flight::redirect($path);
        return false;
    }

    private static function getChangePwd()
    {
        if (getAuth()->isLogin()) {
            $state = getCSRF()->generateCSRFCode();
            self::getResponse()->setResType('view')
                ->setHeader(DefaultController::$defaultViewHeaders)
                ->setView('auth/change_pwd')
                ->setData(['title'=>'Change Password', CSRFCode::$key=>$state])
                ->send();
        } else {
            \Flight::redirect(U(getConfig()->get('defined_routes.login')));
        }
        return false;
    }

    private static function postChangePwd()
    {
        $old = self::getRequest()->getPost('old');
        $new1 = self::getRequest()->getPost('new1');
        $new2 = self::getRequest()->getPost('new2');
        if (!CSRFCode::check()) {
            $msg = Message::getMessage(25);
        } elseif (!$new1 || !$new2){
            $msg = Message::getMessage(15);
        } elseif ($new1 != $new2) {
            $msg = Message::getMessage(22);
        } else {
            if (getAuth()->getAuth()->verify([IAuth::KEY_USERNAME=>getUser(IUser::FIELD_USER_NAME), IAuth::KEY_PASSWORD=>$old])) {
                $user = getAuth()->getUser();
                if ($user && $user->changePassword($new1)) {
                    $msg = Message::getMessage(23);
                } else {
                    $msg = Message::getMessage(24);
                }
            } else {
                $msg = Message::getMessage(24);
            }
        }
        getOValue()->addOldOnce('msg', $msg);
        $rpath = U(getConfig()->get('defined_routes.change_password'));
        \Flight::redirect($rpath);
        return false;
    }

    private static function getSignup()
    {
        if (getAuth()->isLogin()) {
            $path = '/';
            \Flight::redirect($path);
        } else {
            $state = getCSRF()->generateCSRFCode();
            $builder = getCaptcha()->generateCaptcha();
            self::getResponse()->setResType('view')
                ->setHeader(DefaultController::$defaultViewHeaders)
                ->setView('auth/signup')
                ->setData(['title'=>'Sign Up', CSRFCode::$key=>$state, 'captcha'=>$builder])
                ->send();
        }
        return false;
    }

    private static function postSignup()
    {
        $name = self::getRequest()->getPost('username');
        $email = self::getRequest()->getPost('email');
        $pwd = self::getRequest()->getPost('password');
        $pwd2 = self::getRequest()->getPost('password2');
        $phrase = self::getRequest()->getInput('captcha');
        getOValue()->addOld('signup_username', $name);
        getOValue()->addOld('signup_email', $email);
        if (!CSRFCode::check()) {
            $msg = Message::getMessage(25);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.signup'));
            \Flight::redirect($rpath);
            return false;
        } elseif (!getCaptcha()->verifyCaptcha($phrase)) {
            $msg = Message::getMessage(27);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.signup'));
            \Flight::redirect($rpath);
            return false;
        } elseif ($pwd != $pwd2) {
            $msg = Message::getMessage(22);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.signup'));
            \Flight::redirect($rpath);
            return false;
        } elseif (preg_match('/^[_\w]+$/', $name) < 1) {
            $msg = Message::getMessage(30);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.signup'));
            \Flight::redirect($rpath);
            return false;
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
            return false;
        } elseif ($emailexist) {
            $msg = Message::getMessage(29);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.signup'));
            \Flight::redirect($rpath);
            return false;
        }
        $redirectPath = '/';
        $defaultRoles = 'common_user';
        $u = [IUser::FIELD_USER_NAME=>$name, IUser::FIELD_PASSWORD=>$pwd, IUser::FIELD_EMAIL=>$email, IUser::FIELD_ROLES=>$defaultRoles];
        $user = getAuth()->signup($u);
        if ($user) {
            \Flight::redirect($redirectPath);
        } else {
            $msg = Message::getMessage(26);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.signup'));
            \Flight::redirect($rpath);
        }
        return false;
    }

    private static function getForgetPassword()
    {
        if (getAuth()->isLogin()) {
            $path = '/';
            \Flight::redirect($path);
        } else {
            $state = getCSRF()->generateCSRFCode();
            $builder = getCaptcha()->generateCaptcha();
            self::getResponse()->setResType('view')
                ->setHeader(DefaultController::$defaultViewHeaders)
                ->setView('auth/forget_pwd')
                ->setData(['title' => 'Forget Password', CSRFCode::$key => $state, 'captcha' => $builder])
                ->send();
        }
        return false;
    }

    private static function postForgetPassword()
    {
        $email = self::getRequest()->getPost('email');
        $phrase = self::getRequest()->getInput('captcha');
        if (!CSRFCode::check()) {
            $msg = Message::getMessage(25);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.forget_password'));
            \Flight::redirect($rpath);
            return false;
        } elseif (!getCaptcha()->verifyCaptcha($phrase)) {
            $msg = Message::getMessage(27);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.forget_password'));
            \Flight::redirect($rpath);
            return false;
        } elseif (!$email){
            $msg = Message::getMessage(15);
            getOValue()->addOldOnce('msg', $msg);
            $rpath = U(getConfig()->get('defined_routes.forget_password'));
            \Flight::redirect($rpath);
            return false;
        }
        $send = 0;
        //check
        $umodel = getDataPool()->getConnection('auth')->getMapper('User');
        $re = $umodel->get(null, IUser::FIELD_USER_ID, ['AND'=>[IUser::FIELD_EMAIL=>$email]]);
        if ($re) {
            $token = md5($email . ':' . FormatUtils::randStr(20) . time());
            $token_ttl = 86400;
            $u = getDataPool()->getConnection('auth')->getMapper('User')->get(null, [IUser::FIELD_USER_ID, IUser::FIELD_USER_NAME, IUser::FIELD_EMAIL], [IUser::FIELD_EMAIL=>$email]);
            getCache()->set($token, $u, $token_ttl);
            //send email
            $domain = getConfig()->get('domain');
            $body = str_replace(['$domain$', '$token$'], [$domain, $token], self::$forgetEmailBody);
            $mail = getMailer();
            $mail->send(['subject'=>self::$forgetEmailSubject, 'to'=>$email, 'from'=>self::$formatEmailFrom, 'body'=>$body]);
            $send = 1;
        }
        self::getResponse()->setResType('view')
            ->setHeader(DefaultController::$defaultViewHeaders)
            ->setView('auth/forget_pwd')
            ->setData(['title'=>'Forget Password', 'email'=>$email, 'send'=>$send])
            ->send();
        return false;
    }

    private static function getForgetChangePassword()
    {
        $token = self::getRequest()->getInput('token');
        if ($token && getCache()->has($token)) {
            $u = getCache()->get($token);
            $state = getCSRF()->generateCSRFCode();
            $builder = getCaptcha()->generateCaptcha();
            self::getResponse()->setResType('view')
                ->setHeader(DefaultController::$defaultViewHeaders)
                ->setView('auth/forget_change_pwd')
                ->setData(['title' => 'Reset Password', 'user' => $u, CSRFCode::$key=>$state, 'captcha'=>$builder, 'token'=>$token])
                ->send();
        } else {
            \Flight::redirect(U('404'));
        }
        return false;
    }

    private static function postForgetChangePassword()
    {
        $token = self::getRequest()->getInput('token');
        if ($token && getCache()->has($token)) {
            $phrase = self::getRequest()->getInput('captcha');
            $pwd1 = self::getRequest()->getInput('pwd1');
            $pwd2 = self::getRequest()->getInput('pwd2');
            if (!CSRFCode::check()) {
                $msg = Message::getMessage(25);
                getOValue()->addOldOnce('msg', $msg);
                $rpath = self::getRequest()->getRequest()->url;
                \Flight::redirect($rpath);
                return false;
            } elseif (!getCaptcha()->verifyCaptcha($phrase)) {
                $msg = Message::getMessage(27);
                getOValue()->addOldOnce('msg', $msg);
                $rpath = self::getRequest()->getRequest()->url;
                \Flight::redirect($rpath);
                return false;
            } elseif ($pwd1 != $pwd2) {
                $msg = Message::getMessage(22);
                getOValue()->addOldOnce('msg', $msg);
                $rpath = self::getRequest()->getRequest()->url;
                \Flight::redirect($rpath);
                return false;
            } else {
                $u = getCache()->get($token);
                getCache()->delete($token);
                $uname = $u[IUser::FIELD_USER_NAME];
                $email = $u[IUser::FIELD_EMAIL];
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
            self::getResponse()->setResType('view')
                ->setHeader(DefaultController::$defaultViewHeaders)
                ->setView('auth/forget_change_pwd')
                ->setData(['title' => 'Reset Password', 'name' => $uname, 'msg'=>$msg])
                ->send();
        } else {
            \Flight::redirect(U('404'));
        }
        return false;
    }
}