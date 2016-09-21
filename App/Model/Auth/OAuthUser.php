<#php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/9/17 0017
 * Time: 下午 5:53
 */

namespace Wwtg99\App\Model\Auth;


use GuzzleHttp\Client;
use Wwtg99\Flight2wwu\Common\FWException;
use Wwtg99\Flight2wwu\Component\Auth\AuthUser;
use Wwtg99\Flight2wwu\Component\Utils\AjaxRequest;

class OAuthUser extends AuthUser
{

    /**
     * @var bool
     */
    protected $syncUser = false;

    /**
     * @return array
     */
    public function getRoles()
    {
        $r = isset($this->user[UserFactory::KEY_ROLES]) ? $this->user[UserFactory::KEY_ROLES] : [];
        return $r;
    }

    /**
     * TODO
     * Verify user.
     *
     * @param array $user
     * @return bool
     */
    public function verify($user)
    {
        if (isset($user[UserFactory::KEY_USER_TOKEN])) {
            $token = $user[UserFactory::KEY_USER_TOKEN];
            $u = $this->getOAuthUser($token);
            if ($u && !array_key_exists('error', $u)) {
                if ($this->syncUser) {
                    $this->syncUser($u);
                }
                $u[UserFactory::KEY_USER_TOKEN] = $token;
                if (isset($u[UserFactory::KEY_ROLES])) {
                    if (is_array($u[UserFactory::KEY_ROLES])) {
                        $roles = $u[UserFactory::KEY_ROLES];
                    } else {
                        $roles = explode(',', $u[UserFactory::KEY_ROLES]);
                    }
                    if (!in_array('common_user', $roles)) {
                        array_push($roles, 'common_user');
                    }
                    $u[UserFactory::KEY_ROLES] = $roles;
                } else {
                    $u[UserFactory::KEY_ROLES] = ['common_user'];
                }
                $this->user = $u;
                return true;
            }
            if (isset($u['error'])) {
                //TODO handle error
            }
        }
        return false;
    }

    /**
     * TODO
     * Change password.
     *
     * @param $old
     * @param $new
     * @return bool
     */
    public function changePassword($old, $new)
    {
        return false;
    }

    /**
     * TODO
     * Change user info.
     *
     * @param array $user
     * @return bool
     */
    public function changeInfo($user)
    {
        return false;
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
        $uri = 'http://localhost:7280/authorize/user'; //TODO
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