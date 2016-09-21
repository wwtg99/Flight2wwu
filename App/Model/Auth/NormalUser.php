<#php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/9/17 0017
 * Time: 下午 4:41
 */

namespace Wwtg99\App\Model\Auth;


use Wwtg99\Flight2wwu\Component\Auth\AuthUser;

class NormalUser extends AuthUser
{

    /**
     * TODO
     * Verify user.
     *
     * @param array $user
     * @return bool
     */
    public function verify($user)
    {
        if (isset($user['name']) && $user['name'] == 'admin') {
            $this->user = ['user_id'=>'1', 'name'=>'admin', 'superuser'=>true, 'access_token'=>'aaa', 'roles'=>['admin', 'common_user']];
            return true;
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
        if ($old == '1') {
            return true;
        }
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
        return true;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        $r = isset($this->user[UserFactory::KEY_ROLES]) ? $this->user[UserFactory::KEY_ROLES] : [];
        return $r;
    }


}