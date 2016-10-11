<#php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/9/17 0017
 * Time: 下午 4:30
 */

namespace Wwtg99\App\Model\Auth;


use Wwtg99\Flight2wwu\Component\Auth\IAuthUser;

class UserFactory
{

    const KEY_USER_ID = 'user_id';
    const KEY_USER_NAME = 'username';
    const KEY_USER_PASSWORD = 'password';
    const KEY_USER_EMAIL = 'email';
    const KEY_USER_TOKEN = 'access_token';
    const KEY_SUPERUSER = 'superuser';
    const KEY_ROLES = 'roles';
    const KEY_APP_ID = 'app_id';
    const KEY_APP_REDIRECT_URI = 'redirect_uri';
    const KEY_APP_SECRET = 'app_secret';
    const KEY_CODE = 'code';

    /**
     * TODO
     * Return user object.
     *
     * @param $user
     * @return IAuthUser
     */
    public static function getUser($user = [])
    {
        return new NormalUser($user);
//        return new OAuthClientUser($user);  //oauth
    }
}