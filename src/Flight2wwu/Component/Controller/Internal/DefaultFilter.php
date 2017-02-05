<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2017/2/5
 * Time: 15:04
 */

namespace Wwtg99\Flight2wwu\Component\Controller\Internal;


use Wwtg99\PgAuth\Auth\IAuth;
use Wwtg99\PgAuth\Auth\IUser;

class DefaultFilter
{

    /**
     * @param $language
     */
    public static function changeLanguage($language)
    {
        if ($language) {
            \Flight::Locale()->setLocale($language);
            getOValue()->addOld('language', $language);
        } else {
            $language = getOValue()->getOld('language');
            if ($language) {
                \Flight::Locale()->setLocale($language);
            }
        }
    }

    /**
     * @param $ip
     * @param $url
     * @param $method
     * @param $token
     * @param array $skipPaths
     * @return bool
     */
    public static function roleBasedAccessControl($ip, $url, $method, $token = '', $skipPaths = ['/403', '/404'])
    {
        $path = parse_url($url, PHP_URL_PATH);
        // skip /403 and /404
        if (in_array($path, $skipPaths)) {
            return true;
        }
        //login by access_token
        if ($token) {
            getAuth()->getAuth()->tokenTtl = 0;
            getAuth()->setUseCookie(false);
            $user = getAuth()->login([IAuth::KEY_TOKEN => $token]);
            getAuth()->setUseCookie(true);
        }
        // get user
        if (getAuth()->isLogin()) {
            $username = getUser(IUser::FIELD_USER_NAME);
        } else {
            $username = 'anonymous';
        }
        $logger = getLog();
        // log access
        if (!getAuth()->accessPath($path)->access($method)) {
            $logger->changeLogger('access')->info("forbidden from $ip by $username for $path method $method");
            $logger->changeLogger('main');
            return false;
        } else {
            $logger->changeLogger('access')->info("Access from $ip by $username for $path method $method");
            $logger->changeLogger('main');
        }
        return true;
    }

    /**
     * @param $url
     * @param array $skip
     * @param array $allow
     */
    public static function changeLastPath($url, $skip = [], $allow = [])
    {
        $path = parse_url($url, PHP_URL_PATH);
        if ($skip) {
            if (!in_array($path, $skip)) {
                getOValue()->addOldOnce('last_path', $path);
            }
        } else {
            if (in_array($path, $allow)) {
                getOValue()->addOldOnce('last_path', $path);
            }
        }
    }

}