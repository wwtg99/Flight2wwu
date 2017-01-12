<#php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/9/17 0017
 * Time: 下午 4:30
 */

namespace Wwtg99\App\Model\Auth;


use Wwtg99\Flight2wwu\Component\Auth\IAuthUser;
use Wwtg99\PgAuth\Auth\IAuth;
use Wwtg99\PgAuth\Auth\IUser;
use Wwtg99\PgAuth\Auth\NormalAuth;
use Wwtg99\PgAuth\Auth\OAuthServer;

class UserFactory
{

//    const KEY_USER_ID = 'user_id';
//    const KEY_USER_NAME = 'name';
//    const KEY_USER_PASSWORD = 'password';
//    const KEY_USER_EMAIL = 'email';
//    const KEY_USER_TOKEN = 'access_token';
//    const KEY_SUPERUSER = 'superuser';
//    const KEY_ROLES = 'roles';
    const KEY_APP_ID = 'app_id';
    const KEY_APP_REDIRECT_URI = 'redirect_uri';
    const KEY_APP_SECRET = 'app_secret';
    const KEY_CODE = 'code';

    /**
     * @param array $conf
     * @return IAuth
     */
    public static function getAuth($conf = [])
    {
        $m = getConfig()->get('login_method');
        $conn = getDataPool()->getConnection('auth');
        if ($m == 'oauth') {
            return new OAuthServer($conn, $conf);
        } else {
            return new NormalAuth($conn, $conf);
        }
    }
}