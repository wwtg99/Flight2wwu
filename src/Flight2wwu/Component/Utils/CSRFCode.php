<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/11/23
 * Time: 13:53
 */

namespace Wwtg99\Flight2wwu\Component\Utils;


use Wwtg99\Flight2wwu\Common\Request;
use Wwtg99\Flight2wwu\Common\Response;

class CSRFCode
{

    /**
     * @var string
     */
    public static $key = 'csrf_code';

    /**
     * @var int
     */
    protected $ttl = 600;

    /**
     * CSRFCode constructor.
     * @param array $conf
     */
    public function __construct($conf = [])
    {
        $this->ttl = isset($conf['csrf_ttl']) ? $conf['csrf_ttl'] : 600;
    }

    /**
     * @return string
     */
    public function generateCSRFCode()
    {
        $ip = Request::get()->getRequest()->ip;
        $url = Request::get()->getRequest()->url;
        $str = FormatUtils::randStr(10);
        $tm = time();
        $code = md5("CSRF_$ip;$url;$str;$tm");
        getSession()->set($code, 1, $this->ttl);
        return $code;
    }

    /**
     * @param $code
     * @return bool
     */
    public function verifyCSRFCode($code)
    {
        if ($code) {
            $v = getSession()->get($code);
            if ($v === 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * Add CSRF code to response.
     */
    public static function add()
    {
        $res = Response::get();
        $csrf = new CSRFCode();
        $code = $csrf->generateCSRFCode();
        $res->addData(self::$key, $code);
    }

    /**
     * Check CSRF code in request.
     *
     * @return bool
     */
    public static function check()
    {
        $req = Request::get();
        $csrf_code = $req->getInput(self::$key);
        getLog()->warning('====' . $csrf_code);
        $csrf = new CSRFCode();
        return $csrf->verifyCSRFCode($csrf_code);
    }
}