<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/10
 * Time: 18:04
 */

namespace App\Controller;

use DebugBar\StandardDebugBar;
use Flight2wwu\Common\BaseController;
use Flight2wwu\Component\Log\Monolog;

class HomeController extends BaseController
{
    public static function home()
    {
        getView()->render('home');
    }

    /**
     * Log access info
     * @return bool
     */
    public static function access()
    {
        // access log
        $logger = getLog();
        $ip = self::getRequest()->ip;
        $path = self::getRequest()->url;
        $method = self::getRequest()->method;
        if (getAuth()->isLogin()) {
            $user = getUser()['user_id'];
        } else {
            $user = 'anonymous';
        }
        if ($logger instanceof Monolog) {
            $logger->setCurrentLogger('access');
        }
        $logger->info("Access from $ip by $user for $path method $method");
        if ($logger instanceof Monolog) {
            $logger->setCurrentLogger();
        }
        return true;
    }

    /**
     * Switch language
     * @return bool
     */
    public static function language()
    {
        $locale = self::getInput('language');
        if ($locale) {
            \Flight::Locale()->setLocale($locale);
            getLValue()->addOld('language', $locale);
        }
        return true;
    }

    /**
     * Role based access control
     * @return bool
     */
    public static function rbac()
    {
        $skip_path = ['/', '/403', '/auth/login'];
        $path = parse_url(self::getRequest()->url, PHP_URL_PATH);
        if (in_array($path, $skip_path)) {
            return true;
        }
        if (getAuth()->isLogin()) {
            $user = getUser()['user_id'];
        } else {
            $user = 'anonymous';
        }
        $logger = getLog();
        if (!getAuth()->access($path, self::getRequest()->method)) {
            if ($logger instanceof Monolog) {
                $logger->setCurrentLogger('access');
            }
            getLog()->info("forbidden for $path by $user");
            if ($logger instanceof Monolog) {
                $logger->setCurrentLogger();
            }
            \Flight::redirect('/403');
        }
        return true;
    }

    public static function forbidden()
    {
        if (self::getRequest()->ajax) {
            \Flight::json([]);
        } else {
            getView()->render('error/403');
        }
        return false;
    }

    public static function changelog()
    {
        self::defaultHeader();
        $md = new \Parsedown();
        $f = file_get_contents(WEB . 'changelog.txt');
        echo $md->text($f);
    }
} 