<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/23
 * Time: 10:37
 */

namespace Flight2wwu\Component\Auth;

use App\Model\User;

class RoleAuth extends BasicAuth
{
    /**
     * @param string $object
     * @return bool
     */
    public function isVisible($object)
    {
        $right = $this->getAuth($object, 'menu');
        if ($right > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param string $path
     * @param string $method: GET or POST
     * @return bool
     */
    public function access($path, $method = 'GET')
    {
        $method = strtoupper($method);
        $right = $this->getAuth($path, 'path');
        if ($method == 'GET') {
            $m = 1;
        } elseif ($method == 'POST') {
            $m = 2;
        } else {
            $m = 0;
        }
        if (($right & $m) == $m) {
            return true;
        }
        return false;
    }

    /**
     * @param string $object
     * @return bool
     */
    public function isEnable($object)
    {
        $right = $this->getAuth($object, 'object');
        if ($right > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param string $object
     * @param string $type
     * @return int
     */
    public function getAuth($object, $type)
    {
        $uid = getUser('user_id');
        if (!$uid) {
            return 0;
        }
        $auth = User::getAuth($uid, $object, $type);
        return $auth;
    }

    /**
     * @return bool|array
     */
    public function getRoleId()
    {
        $user = $this->getUser();
        if ($user) {
            $id = $user['user_id'];
            return User::getRoles($id);
        }
        return false;
    }

    /**
     * @param string $role_name
     * @return bool
     */
    public function hasRole($role_name)
    {
        $user = $this->getUser();
        if ($user) {
            return User::hasRole($user['user_id'], $role_name);
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isSuperuser()
    {
        $user = $this->getUser();
        if ($user) {
            if ($user['superuser'] == true) {
                return true;
            }
        }
        return false;
    }
} 