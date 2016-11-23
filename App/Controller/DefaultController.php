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
use Wwtg99\Flight2wwu\Common\Response;
use Wwtg99\Flight2wwu\Component\Controller\BaseController;
use Wwtg99\Flight2wwu\Component\Utils\FormatUtils;
use Wwtg99\PgAuth\Auth\IUser;

class DefaultController extends BaseController
{

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
            FormatUtils::formatWebPath(getConfig()->get('defined_routes.login')),
            FormatUtils::formatWebPath(getConfig()->get('defined_routes.logout')),
            FormatUtils::formatWebPath(getConfig()->get('defined_routes.signup')),
        ];
        if (!in_array($path, $skip) && !Request::get()->isAjax()) {
            getOValue()->addOldOnce('last_path', $path);
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
            \Flight::redirect('/403');
            return false;
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
        if (Request::get()->isAjax()) {
            $msg = new Message(403, 'forbidden', 'info');
            $res = Response::get();
            $res->setResCode(403)->setResType('json')->setData($msg->toApiArray());
            return $res->send();
        } else {
            $v = getView();
            if ($v) {
                $v->render('error/403', ['title'=>'authentication failed']);
            } else {
                echo T('forbidden');
            }
        }
        return false;
    }

    /**
     * Method not allowed, error 405
     * @return bool
     */
    public static function methodNotAllowed()
    {
        if (Request::get()->isAjax()) {
            $msg = new Message(405, 'Method not allowed', 'info');
            $res = Response::get();
            $res->setResCode(405)->setResType('json')->setData($msg->toApiArray());
            return $res->send();
        } else {
            \Flight::halt(405, 'Method not allowed');
        }
        return false;
    }

}