<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/22
 * Time: 15:58
 */

namespace Wwtg99\Flight2wwu\Common;

use Wwtg99\App\Model\Auth\UserFactory;
use Wwtg99\App\Model\Message;
use Wwtg99\Flight2wwu\Component\Utils\FormatUtils;

/**
 * Class BaseController
 * Controllers must extends
 * @package Flight2wwu\Common
 */
abstract class BaseController
{
    /**
     * @return \flight\net\Request
     */
    protected static function getRequest()
    {
        return $req = \Flight::request();
    }

    /**
     * @param string $name
     * @param $default
     * @return mixed
     */
    protected static function getInput($name, $default = null)
    {
        $req = self::getRequest();
        if (isset($req->data[$name])) {
            return $req->data[$name];
        }
        if (isset($req->query[$name])) {
            return $req->query[$name];
        }
        return $default;
    }

    /**
     * @param string $name
     * @param $default
     * @return mixed
     */
    protected static function getGet($name, $default = null)
    {
        $req = self::getRequest();
        if (isset($req->query[$name])) {
            return $req->query[$name];
        }
        return $default;
    }

    /**
     * @param string $name
     * @param $default
     * @return mixed
     */
    protected static function getPost($name, $default = null)
    {
        $req = self::getRequest();
        if (isset($req->data[$name])) {
            return $req->data[$name];
        }
        return $default;
    }

    /**
     * Get inputs array by name list.
     *
     * @param array $namelist
     * @param $default
     * @return array
     */
    protected static function getArrayInput(array $namelist, $default = null)
    {
        $out = [];
        foreach ($namelist as $n) {
            $v = self::getInput($n, $default);
            $out[$n] = $v;
        }
        return $out;
    }

    /**
     * Get Inputs array by name list without null.
     *
     * @param array $namelist
     * @return array
     */
    protected static function getArrayInputN(array $namelist)
    {
        $out = [];
        foreach ($namelist as $n) {
            $v = self::getInput($n);
            if (!is_null($v)) {
                $out[$n] = $v;
            }
        }
        return $out;
    }

    /**
     * Check value.
     *
     * @param $val
     * @param string $type
     * @param bool $throws
     * @return bool
     * @throws FWException
     */
    protected static function checkExists($val, $type = null, $throws = true)
    {
        $pass = true;
        $msg = '';
        if (is_null($val)) {
            $pass = false;
            $msg = 'Value is null!';
        } elseif ($type) {
            if (gettype($val) != $type) {
                $pass = false;
                $msg = 'Type does not match!';
            }
        }
        if ($throws) {
            if (!$pass) {
                throw new FWException($msg, 1);
            }
        }
        return $pass;
    }

    /**
     * Check input exists, return value or error array.
     *
     * @param string $name
     * @param string $message
     * @param int $code
     * @return Message|string
     */
    protected static function checkInput($name, $message = '', $code = 11)
    {
        $req = self::getRequest();
        if (isset($req->data[$name])) {
            return $req->data[$name];
        }
        if (isset($req->query[$name])) {
            return $req->query[$name];
        }
        if (!$message) {
            $message = "$name does not exists";
        }
        return new Message($code, $message);
    }

    /**
     * Check input exists in namelist or error array.
     *
     * @param array $namelist
     * @param array $message: ['name'=>['message'=>'', 'code'=>''], ...]
     * @return Message|bool
     */
    protected static function checkInputs(array $namelist, array $message)
    {
        foreach ($namelist as $item) {
            if (array_key_exists($item, $message)) {
                $msg = $message[$item];
            } else {
                $msg = ['message'=>"$item does not exists", 'code'=>1];
            }
            $re = self::checkInput($item, $msg['message'], $msg['code']);
            if ($re instanceof Message) {
                return $re;
            }
        }
        return true;
    }

    /**
     * params method1, method2, ...
     * @return bool
     */
    protected static function checkMethod()
    {
        $md = self::getRequest()->method;
        $methods = func_get_args();
        foreach ($methods as $m) {
            if ($md == strtoupper($m)) {
                return true;
            }
        }
        return false;
    }

    /**
     * default header
     */
    protected static function defaultHeader()
    {
        header('Cache-Control: no-cache');
        header('Pragma: no-cache');
    }

    /**
     * @return string
     */
    protected static function generateCSRFState()
    {
        $ip = self::getRequest()->ip;
        $url = self::getRequest()->url;
        $uid = getUser(UserFactory::KEY_USER_ID);
        $str = FormatUtils::randStr(10);
        $tm = time();
        $state = md5("CSRF_$ip;$url;$uid;$str;$tm");
        $state_time = 600;
        getCache()->set($state, 1, $state_time);
        return $state;
    }

    /**
     * @param $state
     * @return bool
     */
    protected static function verifyCSRFState($state)
    {
        if ($state) {
            $v = getCache()->get($state);
            if ($v === 1) {
                return true;
            }
        }
        return false;
    }
} 