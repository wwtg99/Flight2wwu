<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/10/8
 * Time: 10:47
 */

namespace Wwtg99\App\Model\Auth;

/**
 * Class User
 * @package wwu\App\Model
 */
class User
{

//    use TNormalAuth;
//    use TOpenAuth; //should disable admin departments/users/roles, change password and edit user info

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
     * //TODO
     * The function to verify user.
     *
     * @param array $user
     * @return bool
     */
    public function verify($user)
    {
        //TODO
        $this->user = $user;
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