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
     * @param string $path
     * @param string $method: GET or POST
     * @return bool
     */
    public function access($path, $method = 'GET')
    {
        $method = strtoupper($method);
        $right = $this->getPathAuth($path);
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
     * @return int
     */
    public function getAuth($object)
    {
        if ($this->isLogin()) {
            $r = $this->getRoles();
            $roles = [];
            foreach ($r as $rr) {
                array_push($roles, $rr['name']);
            }
        } else {
            $roles = 'anonymous';
        }
        $rbac = \Flight::Rbac();
        return $rbac->getAuth($roles, $object);
    }

    /**
     * @param string $path
     * @return int
     */
    public function getPathAuth($path)
    {
        if ($this->isLogin()) {
            $r = $this->getRoles();
            $roles = [];
            foreach ($r as $rr) {
                array_push($roles, $rr['name']);
            }
        } else {
            $roles = 'anonymous';
        }
        $rbac = \Flight::Rbac();
        return $rbac->getPathAuth($roles, $path);
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        $user = $this->getUser();
        if ($user) {
            if (!array_key_exists('roles', $user)) {
                $user['roles'] = User::getRoles($user['user_id']);
                $this->login($user);
            }
            return $user['roles'];
        }
        return [];
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