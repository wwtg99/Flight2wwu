<#php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/9/17 0017
 * Time: 下午 4:30
 */

namespace Wwtg99\App\Model\Auth;


use Wwtg99\PgAuth\Auth\IAuth;
use Wwtg99\PgAuth\Auth\NormalAuth;
use Wwtg99\PgAuth\Auth\OAuthServer;

class UserFactory
{

    const KEY_APP_ID = 'app_id';
    const KEY_APP_REDIRECT_URI = 'redirect_uri';
    const KEY_APP_SECRET = 'app_secret';
    const KEY_CODE = 'code';

    /**
     * Get your own auth if necessary TODO
     *
     * @param array $conf
     * @return IAuth
     */
    public static function getAuth($conf = [])
    {
        $m = getConfig()->get('login_method');
        $conn = getDataPool()->getConnection('auth');
        if ($m == 'oauth') {
            return new OAuthClient($conn, $conf);
        } else {
            return new NormalAuth($conn, $conf);
        }
    }

    /**
     * @param array $conf
     * @return OAuthServer
     */
    public static function getOAuthServer($conf = [])
    {
        $conn = getDataPool()->getConnection('auth');
        if (!$conf) {
            $conf = \Flight::get('config')->get('auth');
        }
        return new OAuthServer($conn, $conf);
    }

    /**
     * @param array $conf
     * @return NormalAuth
     */
    public static function getNormalAuth($conf = [])
    {
        $conn = getDataPool()->getConnection('auth');
        if (!$conf) {
            $conf = \Flight::get('config')->get('auth');
        }
        return new NormalAuth($conn, $conf);
    }
}