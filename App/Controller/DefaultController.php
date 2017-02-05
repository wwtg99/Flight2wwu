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
use Wwtg99\Flight2wwu\Component\Controller\Internal\DefaultFilter;
use Wwtg99\Flight2wwu\Component\Utils\FormatUtils;
use Wwtg99\PgAuth\Auth\IAuth;
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
        DefaultFilter::changeLanguage($locale);
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
        $tokenKey = getConfig()->get('access_token_key');
        $token = self::getRequest()->getInput($tokenKey);
        $re = DefaultFilter::roleBasedAccessControl($ip, $url, $method, $token);
        if ($re) {
            // last path
            $skip = [
                '/oauth/redirect_login',
                FormatUtils::formatWebPath(getConfig()->get('defined_routes.login')),
                FormatUtils::formatWebPath(getConfig()->get('defined_routes.logout')),
                FormatUtils::formatWebPath(getConfig()->get('defined_routes.signup')),
            ];
            DefaultFilter::changeLastPath($url, $skip);
            return true;
        } else {
            return self::forbidden();
        }
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