<#php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/9/17 0017
 * Time: 下午 5:53
 */

namespace Wwtg99\App\Model\Auth;


use Wwtg99\Flight2wwu\Component\Auth\PgAuthUser;
use Wwtg99\Flight2wwu\Component\Utils\AjaxRequest;
use Wwtg99\PgAuth\Auth\OAuthUser;

class OAuthClientUser extends PgAuthUser
{

    /**
     * @var bool
     */
    protected $syncUser = false;

    /**
     * OAuthUser constructor.
     * @param $user
     */
    public function __construct($user)
    {
        parent::__construct($user);
        if (is_array($user)) {
            $this->user = new OAuthUser($user);
        }
    }

    /**
     * TODO
     * Verify user.
     *
     * @param array $user
     * @return bool
     */
    public function verify(array $user)
    {
        if (isset($user[UserFactory::KEY_USER_TOKEN])) {
            $token = $user[UserFactory::KEY_USER_TOKEN];
            $u = $this->getOAuthUser($token);
            if ($u && !array_key_exists('error', $u)) {
                if ($this->syncUser) {
                    $this->syncUser($u);
                }
                $this->user = new OAuthUser($u);
                return true;
            }
            if (isset($u['error'])) {
                //TODO handle error
            }
        }
        return false;
    }

    /**
     * @param $old
     * @param $new
     * @return bool
     */
    public function changePassword($old, $new)
    {
        return false;
    }

    /**
     * @param array $user
     * @return bool
     */
    public function changeInfo(array $user)
    {
        return false;
    }

    /**
     * @param array $user
     * @return bool
     */
    public function signUp(array $user)
    {
        return false;
    }

    /**
     * @param array $user
     * @return bool
     */
    public function login(array $user)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function logout()
    {
        return true;
    }

    /**
     * TODO
     * Sync oauth user to ours.
     *
     * @param $user
     */
    public function syncUser($user)
    {

    }

    /**
     * @param string $token
     * @return array
     */
    protected function getOAuthUser($token)
    {
        $uri = 'http://localhost:9111/authorize/user'; //TODO
        $res = $this->getResource($uri, $token);
        return $res;
    }

    /**
     * @param $api_uri
     * @param $token
     * @param $method
     * @param array $params
     * @return array
     */
    protected function getResource($api_uri, $token, $method = 'GET', $params = [])
    {
        $oauth = getConfig()->get('oauth');
        $appid = isset($oauth['app_id']) ? $oauth['app_id'] : '';
        $appkey = isset($oauth['app_id_key']) ? $oauth['app_id_key'] : '';
        if ($appid) {
            $params[$appkey] = $appid;
        }
        $params['access_token'] = $token;
        $client = new AjaxRequest([], 'json');
        if ($method == 'GET') {
            $res = $client->get($api_uri, $params);
        } elseif ($method == 'POST') {
            $res = $client->post($api_uri, $params);
        } else {
            $res = [];
        }
        return $res;
    }

}