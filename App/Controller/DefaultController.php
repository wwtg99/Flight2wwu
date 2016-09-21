<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/8/1
 * Time: 11:23
 */

namespace Wwtg99\App\Controller;


use Wwtg99\App\Model\Auth\UserFactory;
use Wwtg99\Flight2wwu\Common\BaseController;
use Wwtg99\Flight2wwu\Component\Utils\FormatUtils;

class DefaultController extends BaseController
{

    /**
     * Switch language
     * @return bool
     */
    public static function language()
    {
        $locale = self::getInput('language');
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
        $ip = self::getRequest()->ip;
        $url = self::getRequest()->url;
        $method = self::getRequest()->method;
        $path = parse_url($url, PHP_URL_PATH);
        // skip /403
        if ($path == '/403') {
            return true;
        }
        // last path
        $skip = [
            '/404',
            '/oauth/redirect_login',
            FormatUtils::formatWebPath(getConfig()->get('defined_routes.login')),
            FormatUtils::formatWebPath(getConfig()->get('defined_routes.logout'))
        ];
        if (!in_array($path, $skip) && !self::getRequest()->ajax) {
            getOValue()->addOldOnce('last_path', $path);
        }
        // get user
        if (getAuth()->isLogin()) {
            $user = getUser()[UserFactory::KEY_USER_ID];
        } else {
            $user = 'anonymous';
        }
        $logger = getLog();
        // log access
        if (!getAuth()->accessPath($path)->access(self::getRequest()->method)) {
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
        if (self::getRequest()->ajax) {
            \Flight::json([]);
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

}