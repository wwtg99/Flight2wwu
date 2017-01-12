<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/8/1
 * Time: 11:23
 */

namespace Wwtg99\App\Controller;


use Wwtg99\App\Model\Message;
use Wwtg99\Flight2wwu\Common\Request;
use Wwtg99\Flight2wwu\Component\Controller\BaseController;
use Wwtg99\Flight2wwu\Component\Utils\FormatUtils;
use Wwtg99\PgAuth\Auth\IUser;

class DefaultController extends BaseController
{

    /**
     * @var array
     */
    public static $defaultApiHeaders = [
        'Access-Control-Allow-Origin: *',
        'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, PATCH, DELETE',
        'Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept',
    ];

    /**
     * @var array
     */
    public static $defaultViewHeaders = [
        'Cache-Control: no-cache',
    ];

    /**
     * Switch language
     * @return bool
     */
    public static function language()
    {
        $locale = Request::get()->getInput('language');
        if ($locale) {
            \Flight::Locale()->setLocale($locale);
            getOValue()->addOld('language', $locale);
        } else {
            $locale = getOValue()->getOld('language');
            if ($locale) {
                \Flight::Locale()->setLocale($locale);
            }
        }
        return true;
    }

    /**
     * Role based access control and access log
     * @return bool
     */
    public static function rbac()
    {
        $ip = Request::get()->getRequest()->ip;
        $url = Request::get()->getRequest()->url;
        $method = Request::get()->getRequest()->method;
        $path = parse_url($url, PHP_URL_PATH);
        // skip /403 and /404
        if ($path == '/403' || $path == '/404') {
            return true;
        }
        // last path
        $skip = [
            '/oauth/redirect_login',
            FormatUtils::formatWebPath(getConfig()->get('defined_routes.login')),
            FormatUtils::formatWebPath(getConfig()->get('defined_routes.logout')),
            FormatUtils::formatWebPath(getConfig()->get('defined_routes.signup')),
        ];
        if (!in_array($path, $skip) && !Request::get()->isAjax()) {
            getOValue()->addOldOnce('last_path', $path);
        }
        //login by access_token
        $tokenLogin = getConfig()->get('access_token_login');
        if ($tokenLogin) {
            $tokenKey = getConfig()->get('access_token_key');
            $token = self::getRequest()->getInput($tokenKey);
            if (getAuth()->getAuth()->verify([\Wwtg99\PgAuth\Auth\IAuth::KEY_TOKEN=>$token])) {
                getAuth()->setUser(getAuth()->getAuth()->getUser());
            }
        }
        // get user
        if (getAuth()->isLogin()) {
            $user = getUser()[IUser::FIELD_USER_NAME];
        } else {
            $user = 'anonymous';
        }
        $logger = getLog();
        // log access
        if (!getAuth()->accessPath($path)->access(Request::get()->getMethod())) {
            $logger->changeLogger('access')->info("forbidden from $ip by $user for $path method $method");
            $logger->changeLogger('main');
            return self::forbidden();
        } else {
            $logger->changeLogger('access')->info("Access from $ip by $user for $url method $method");
            $logger->changeLogger('main');
        }
        return true;
    }

    /**
     * Forbidden, error 403
     * @return bool
     */
    public static function forbidden()
    {
        $res = self::getResponse()->setResCode(403);
        if (Request::get()->isAjax()) {
            $msg = new Message(403, 'forbidden', 'info');
            return $res->setHeader(DefaultController::$defaultApiHeaders)->setResType('json')->setData($msg->toApiArray())->send();
        } else {
            return $res->setHeader(DefaultController::$defaultViewHeaders)->setResType('view')->setView('error/403')->setData(['title'=>'authentication failed'])->send();
        }
    }

    /**
     * Method not allowed, error 405
     * @return bool
     */
    public static function methodNotAllowed()
    {
        $res = self::getResponse()->setResCode(405);
        if (Request::get()->isAjax()) {
            $msg = new Message(405, 'Method not allowed', 'info');
            return $res->setHeader(DefaultController::$defaultApiHeaders)->setResType('json')->setData($msg->toApiArray())->send();
        } else {
            \Flight::halt(405, 'Method not allowed');
        }
        return false;
    }

}