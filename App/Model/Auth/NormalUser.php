<#php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/9/17 0017
 * Time: 下午 4:41
 */

namespace Wwtg99\App\Model\Auth;


use Wwtg99\Flight2wwu\Component\Auth\PgAuthUser;
use Wwtg99\PgAuth\Auth\NormalAuth;

class NormalUser extends PgAuthUser
{

    /**
     * NormalUser constructor.
     *
     * @param $user
     */
    public function __construct($user)
    {
        parent::__construct($user);
        $this->init($user);
    }

    /**
     * @param array $user
     */
    private function init($user = [])
    {
        $cache = getConfig()->get('auth_cache');
        $ttl = getConfig()->get('token_ttl');
        $conf = [
            'cache'=>$cache,
            'token_ttl'=>$ttl,
            'key_user_name'=>UserFactory::KEY_USER_NAME,
            'key_password'=>UserFactory::KEY_USER_PASSWORD,
            'key_access_token'=>UserFactory::KEY_USER_TOKEN,
        ];
        $this->auth = new NormalAuth(getDataPool()->getConnection('auth'), $conf);
        if ($user && is_array($user)) {
            $this->user = new \Wwtg99\PgAuth\Auth\NormalUser($user, getDataPool()->getConnection('auth'), $this->auth);
        }
    }

}