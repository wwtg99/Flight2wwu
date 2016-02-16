<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/10/8
 * Time: 10:47
 */

namespace App\Model;

/**
 * Class User
 * @package wwu\App\Model
 */
class User
{
    /**
     * Verify user.
     *
     * @param array $user
     * @return bool|array
     */
    public static function verify($user)
    {
        $db = getDB();
        if (array_key_exists('token', $user)) {
            $u = $db->queryOne('select id, name, label, email, superuser, descr, department from users where remember_token = :token and active is true', ['token'=>$user['token']]);
            if ($u) {
                if (self::verifyToken($user['token'], $u['id'])) {
                    $roles = self::getRoles($u['id']);
                    return ['user_id'=>$u['id'], 'username'=>$u['name'], 'label'=>$u['label'], 'department'=>$u['department'], 'email'=>$u['email'], 'superuser'=>$u['superuser'], 'descr'=>$u['descr'], 'token'=>$user['token'], 'roles'=>$roles];
                }
            }
        } else {
            $u = $db->queryOne('select id, label, email, password, superuser, descr, department from users where name = :name and active is true', ['name' => $user['username']]);
            if ($u) {
                $uid = $u['id'];
                $pwd = $u['password'];
                $age = $user['remember'] ? 30 : 1;
                if (is_null($pwd) || $pwd === '' || password_verify($user['password'], $pwd)) {
                    $token = User::generateToken($uid, $age);
                    User::updateToken($uid, $token);
                    $roles = self::getRoles($uid);
                    return ['user_id' => $uid, 'username' => $user['username'], 'label'=>$u['label'], 'department'=>$u['department'], 'email' => $u['email'], 'superuser' => $u['superuser'], 'descr' => $u['descr'], 'token' => $token, 'roles'=>$roles];
                }
            }
        }
        return false;
    }

    /**
     * @param array $user
     * @return bool
     */
    public static function check($user)
    {
        $db = getDB();
        if (array_key_exists('user_id', $user)) {
            $u = $db->queryOne('select password from users where id = :id and active is true', ['id'=>$user['user_id']]);
        } elseif (array_key_exists('username', $user)) {
            $u = $db->queryOne('select password from users where name = :name and active is true', ['name'=>$user['username']]);
        } else {
            $u = [];
        }
        if ($u) {
            $pwd = $u['password'];
            if (is_null($pwd) || $pwd === '' || password_verify($user['password'], $pwd)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $user_id
     * @param string $old
     * @param string $new
     * @return bool
     */
    public static function changePassword($user_id, $old, $new)
    {
        if (User::check(['user_id'=>$user_id, 'password'=>$old])) {
            $db = getDB();
            $pwd = password_hash($new, PASSWORD_BCRYPT);
            $re = $db->exec('update users set password = :pwd where id = :id', ['pwd'=>$pwd, 'id'=>$user_id]);
            if ($re) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $uid
     * @return array
     */
    public static function getUser($uid)
    {
        $db = getDB();
        $re = $db->queryOne("select id as user_id, name as username, label, email, department, superuser, descr, token from users where id = :uid", ['uid'=>$uid]);
        if ($re) {
            $roles = self::getRoles($uid);
            $re['roles'] = $roles;
            return $re;
        }
        return [];
    }

    /**
     * @param array $user
     * @return bool
     */
    public static function updateInfo($user)
    {
        $uid = $user['user_id'];
        $db = getDB();
        $sets = [];
        foreach ($user as $k => $v) {
            if ($k == 'user_id' || $k == 'password') {
                continue;
            }
            array_push($sets, "$k = $v");
        }
        $s = implode(',', $sets);
        $re = $db->exec("update users set $s where id = :uid", ['uid'=>$uid]);
        if ($re) {
            return true;
        }
        return false;
    }

    /**
     * @param string $uid
     * @return array
     */
    public static function getRoles($uid)
    {
        $db = getDB();
        $re = $db->query("select role_id, roles.name from user_role join roles on user_role.role_id = roles.id where user_id = :uid", ['uid'=>$uid]);
        if ($re) {
            return $re;
        }
        return [];
    }

    /**
     * @param string $uid
     * @param string $role_name
     * @return bool
     */
    public static function hasRole($uid, $role_name)
    {
        $db = getDB();
        $re = $db->queryOne("select role_id from user_role join roles on user_role.role_id = roles.id where roles.name = :name and user_role.user_id = :uid", ['name'=>$role_name, 'uid'=>$uid]);
        if ($re) {
            return true;
        }
        return false;
    }

    /**
     * @param $user_id
     * @param $role_id
     * @return bool
     */
    public static function addRole($user_id, $role_id)
    {
        $db = getDB();
        $re = $db->queryOne("select add_role(:uid, :rid)", ['uid'=>$user_id, 'rid'=>$role_id]);
        if ($re && $re['add_role']) {
            return true;
        }
        return false;
    }

    /**
     * @param $user_id
     * @param $role_id
     * @return bool
     */
    public static function removeRole($user_id, $role_id)
    {
        $db = getDB();
        $re = $db->queryOne("select remove_role(:uid, :rid)", ['uid'=>$user_id, 'rid'=>$role_id]);
        if ($re && $re['remove_role']) {
            return true;
        }
        return false;
    }

    /**
     * @param $user_id
     * @param array $roles
     */
    public static function changeRoles($user_id, array $roles)
    {
        $db = getDB();
        $db->begin();
        $db->exec("delete from user_role where user_id = :uid", ['uid'=>$user_id]);
        foreach ($roles as $rid) {
            $db->exec("insert into user_role (user_id, role_id) values (:uid, :rid)", ['uid'=>$user_id, 'rid'=>$rid]);
        }
        $db->commit();
    }

    /**
     * @param string $uid
     * @param int $age
     * @return string
     */
    private static function generateToken($uid, $age = 1)
    {
        $ip = \Flight::request()->ip;
        if (is_int($age)) {
            $time = strtotime("+$age day");
        } else {
            $time = strtotime("+1 day");
        }
        return base64_encode(implode(';', [$uid, $ip, $time]));
    }

    /**
     * @param string $token
     * @param string $uid
     * @return bool
     */
    private static function verifyToken($token, $uid)
    {
        if (!$token) {
            return false;
        }
        $code = base64_decode($token);
        $c = explode(';', $code);
        if ($uid != $c[0]) {
            return false;
        }
        if ($c[1] != \Flight::request()->ip) {
            return false;
        }
        if ($c[2] < time()) {
            return false;
        }
        return true;
    }

    /**
     * @param string $uid
     * @param string $token
     * @return bool|int
     */
    private static function updateToken($uid, $token)
    {
        $db = getDB();
        $re = $db->exec('update users set remember_token = :token where id = :uid', ['token'=>$token, 'uid'=>$uid]);
        return $re;
    }
} 