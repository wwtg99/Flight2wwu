<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/11/23
 * Time: 15:25
 */

namespace Wwtg99\Flight2wwu\Common;


use Wwtg99\App\Model\Message;

class Request
{

    /**
     * @var Request
     */
    private static $instance = null;

    /**
     * @var \flight\net\Request
     */
    protected $request;

    /**
     * Request constructor.
     */
    private function __construct()
    {
        $this->request = \Flight::request();
    }

    /**
     * @return Request
     */
    public static function get()
    {
        if (!self::$instance) {
            self::$instance = new Request();
        }
        return self::$instance;
    }

    /**
     * @return \flight\net\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param string $name
     * @param $default
     * @return mixed
     */
    public function getInput($name, $default = null)
    {
        if (isset($this->request->data[$name])) {
            return $this->request->data[$name];
        }
        if (isset($this->request->query[$name])) {
            return $this->request->query[$name];
        }
        return $default;
    }

    /**
     * @param string $name
     * @param $default
     * @return mixed
     */
    public function getGet($name, $default = null)
    {
        if (isset($this->request->query[$name])) {
            return $this->request->query[$name];
        }
        return $default;
    }

    /**
     * @param string $name
     * @param $default
     * @return mixed
     */
    public function getPost($name, $default = null)
    {
        if (isset($this->request->data[$name])) {
            return $this->request->data[$name];
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
    public function getArrayInput(array $namelist, $default = null)
    {
        $out = [];
        foreach ($namelist as $n) {
            $v = $this->getInput($n, $default);
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
    public function getArrayInputN(array $namelist)
    {
        $out = [];
        foreach ($namelist as $n) {
            $v = $this->getInput($n);
            if (!is_null($v)) {
                $out[$n] = $v;
            }
        }
        return $out;
    }

    /**
     * Check input exists, return value or error array.
     *
     * @param string $name
     * @param string $message
     * @param int $code
     * @return Message|string
     */
    public function checkInput($name, $message = '', $code = 11)
    {
        if (isset($this->request->data[$name])) {
            return $this->request->data[$name];
        }
        if (isset($this->request->query[$name])) {
            return $this->request->query[$name];
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
    public function checkInputs(array $namelist, array $message)
    {
        foreach ($namelist as $item) {
            if (array_key_exists($item, $message)) {
                $msg = $message[$item];
            } else {
                $msg = ['message'=>"$item does not exists", 'code'=>1];
            }
            $re = $this->checkInput($item, $msg['message'], $msg['code']);
            if ($re instanceof Message) {
                return $re;
            }
        }
        return true;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        $md = $this->request->method;
        $override = getConfig()->get('route.override_http_method');
        if ($override) {
            if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
                $md = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
            }
        }
        return $md;
    }

    /**
     * params method1, method2, ...
     * @return bool
     */
    public function checkMethod()
    {
        $md = $this->getMethod();
        $methods = func_get_args();
        foreach ($methods as $m) {
            if ($md == strtoupper($m)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        return $this->request->ajax;
    }


    /**
     * @return bool
     */
    function isMobile()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA']))
        {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        $uagent = $this->request->user_agent;
        if ($uagent)
        {
            $clientkeywords = array ('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($uagent)))
            {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT']))
        {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
            {
                return true;
            }
        }
        return false;
    }

}