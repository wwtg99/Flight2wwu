<#php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/9/17 0017
 * Time: 下午 4:30
 */

namespace Wwtg99\App\Model\Auth;


use Wwtg99\Flight2wwu\Component\Auth\AuthUser;

class UserFactory
{

    const KEY_USER_ID = 'user_id';
    const KEY_USER_NAME = 'name';
    const KEY_USER_PASSWORD = 'password';
    const KEY_USER_EMAIL = 'email';
    const KEY_USER_TOKEN = 'access_token';
    const KEY_SUPERUSER = 'superuser';
    const KEY_ROLES = 'roles';
    const TABLE_USER = 'users';

    /**
     * TODO
     * Return user object.
     *
     * @param array $user
     * @return AuthUser
     */
    public static function getUser($user = [])
    {
        return new NormalUser($user);
//        return new OAuthUser($user);  //oauth
    }
}