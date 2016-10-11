<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/10/9
 * Time: 17:05
 */

namespace Wwtg99\App\Model\Auth;


use Wwtg99\Flight2wwu\Component\Auth\PgAuthUser;
use Wwtg99\PgAuth\Auth\OAuthServer;
use Wwtg99\PgAuth\Auth\OAuthUser;

class OAuthServerUser extends PgAuthUser
{

    const CODE_EXPIRES = 180;

    /**
     * OAuthServerUser constructor.
     * @param $user
     */
    public function __construct($user)
    {
        parent::__construct($user);
        $this->init($user);
    }

    /**
     * @param array $user
     * @return null|string
     */
    public function getCode(array $user)
    {
        if ($this->auth instanceof OAuthServer) {
            return $this->auth->getCode($user);
        }
        return null;
    }

    /**
     * @param $user
     */
    private function init($user)
    {
        $cache = getConfig()->get('auth_cache');
        $ttl = getConfig()->get('token_ttl');
        $conf = [
            'cache'=>$cache,
            'token_ttl'=>$ttl,
            'code_ttl'=>self::CODE_EXPIRES,
            'key_user_name'=>UserFactory::KEY_USER_NAME,
            'key_password'=>UserFactory::KEY_USER_PASSWORD,
            'key_access_token'=>UserFactory::KEY_USER_TOKEN,
            'key_app_id'=>UserFactory::KEY_APP_ID,
            'key_app_redirect_uri'=>UserFactory::KEY_APP_REDIRECT_URI,
            'key_app_secret'=>UserFactory::KEY_APP_SECRET,
            'key_code'=>UserFactory::KEY_CODE,
        ];
        $this->auth = new OAuthServer(getDataPool()->getConnection('auth'), $conf);
        if ($user && is_array($user)) {
            $this->user = new OAuthUser($user);
        }
    }

}