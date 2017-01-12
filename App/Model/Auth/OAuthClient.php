<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2017/1/12
 * Time: 16:43
 */

namespace Wwtg99\App\Model\Auth;


use Wwtg99\Flight2wwu\Component\Utils\AjaxRequest;
use Wwtg99\PgAuth\Auth\IAuth;
use Wwtg99\PgAuth\Auth\IUser;
use Wwtg99\PgAuth\Auth\NormalAuth;
use Wwtg99\PgAuth\Auth\NormalUser;

class OAuthClient extends NormalAuth
{

    /**
     * TODO get user info
     * @var string
     */
    protected $verifyUri = 'http://localhost:8680/user/info';

    /**
     * @param array $user
     * @return IUser|null
     */
    public function signUp(array $user)
    {
        return null;
    }

    /**
     * @param array $user
     * @return IUser|null
     */
    public function signIn(array $user)
    {
        if ($this->verify($user)) {
            $this->msg = 'Sign in successfully!';
        }
        return $this->user;
    }

    /**
     * @param array $user
     * @return IUser|null
     */
    public function signOut(array $user)
    {
        $this->msg = 'Sign out successfully!';
        return null;
    }

    /**
     * @param array $user
     * @return bool
     */
    public function verify(array $user)
    {
        if (isset($user[IAuth::KEY_TOKEN])) {
            $token = $user[IAuth::KEY_TOKEN];
            $api = new AjaxRequest();
            $res = $api->get($this->verifyUri, ['access_token'=>$token]);
            if ($res && $res->getStatusCode() == 200) {
                $u = \GuzzleHttp\json_decode($res->getBody(), true);
                if ($u && isset($u[IUser::FIELD_USER_ID])) {
                    $this->user = new NormalUser($u[IUser::FIELD_USER_ID], $u, $token);
                    return true;
                }
            }
        }
        return false;
    }


}