<#php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/9/17 0017
 * Time: 下午 5:53
 */

namespace Wwtg99\App\Model\Auth;


use GuzzleHttp\Client;
use Wwtg99\Flight2wwu\Component\Auth\AuthUser;

class OAuthUser extends AuthUser
{

    /**
     * @var bool
     */
    protected $syncUser = false;

    /**
     * AuthUser constructor.
     * @param array $user
     */
    public function __construct(array $user)
    {
        if ($user) {
            $roles = isset($user[UserFactory::KEY_ROLES]) ? $user[UserFactory::KEY_ROLES] : [];
            if (!in_array('common_user', $roles)) {
                array_push($roles, 'common_user');
            }
            $user[UserFactory::KEY_ROLES] = $roles;
        }
        parent::__construct($user);
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        $r = isset($this->user[UserFactory::KEY_ROLES]) ? $this->user[UserFactory::KEY_ROLES] : [];
        return $r;
    }

    /**
     * Verify user.
     *
     * @param array $user
     * @return bool
     */
    public function verify($user)
    {
        if (isset($user['access_token'])) {
            $token = $user['access_token'];
            $u = $this->getOAuthUser($token);
            if ($u && !array_key_exists('error', $u)) {
                if ($this->syncUser) {
                    $this->syncUser($u);
                }
                $u[User::KEY_USER_TOKEN] = $token;
                $this->user = $u;
                return true;
            }
        }
        return false;
    }

    /**
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
        $uri = ''; //TODO
        $res = $this->getResource($uri, $token);
        return $res;
    }

    /**
     * @param $api_uri
     * @param $token
     * @param array $params
     * @return array
     */
    protected function getResource($api_uri, $token, $params = [])
    {
        $oauth = getConfig()->get('oauth');
        $appid = isset($oauth['app_id']) ? $oauth['app_id'] : '';
        if ($appid) {
            $params['app_key'] = $appid;
        }
        $params['access_token'] = $token;
        $client = new Client();
        $res = $client->request('GET', $api_uri, ['query'=>$params]);
        return json_decode($res->getBody(), true);
    }

}