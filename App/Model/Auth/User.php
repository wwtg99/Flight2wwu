<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/10/8
 * Time: 10:47
 */

namespace Wwtg99\App\Model\Auth;

/**
 * Class User
 * @deprecated
 * @package wwu\App\Model
 */
class User
{

    const KEY_USER_ID = 'user_id';
    const KEY_USER_NAME = 'name';
    const KEY_USER_PASSWORD = 'password';
    const KEY_USER_EMAIL = 'email';
    const KEY_USER_TOKEN = 'remember_token';
    const KEY_SUPERUSER = 'superuser';
    const KEY_ROLES = 'roles';
    const TABLE_USER = 'users';

    /**
     * @var array
     */
    private $user = [];

    /**
     * User constructor.
     * @param array $user
     */
    public function __construct(array $user = [])
    {
        $this->user = $user;
    }

    /**
     * TODO
     * The function to verify user.
     *
     * @param array $user
     * @return bool
     */
    public function verify($user)
    {
        if (isset($user['name']) && $user['name'] == 'admin') {
            $this->user = ['user_id'=>'1', 'name'=>'admin', 'superuser'=>true, 'roles'=>['admin'], 'remember_token'=>'aaa'];
            return true;
        }
        return false;
    }

    /**
     * TODO
     * Change Password
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
     * Change user info
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
    public function getUser()
    {
        return $this->user;
    }

} 