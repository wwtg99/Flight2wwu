<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/11/23
 * Time: 13:49
 */

namespace Wwtg99\Flight2wwu\Component\Utils;


use Gregwar\Captcha\CaptchaBuilder;
use Wwtg99\Flight2wwu\Common\Request;

class Captcha
{

    /**
     * @var int
     */
    protected $ttl = 600;

    /**
     * Captcha constructor.
     */
    public function __construct()
    {
        $this->ttl = isset($conf['ttl']) ? $conf['ttl'] : 600;
    }

    /**
     * @return CaptchaBuilder
     */
    public function generateCaptcha()
    {
        $ip = Request::get()->getRequest()->ip;
        $builder = new CaptchaBuilder();
        $builder->build();
        $key = md5($builder->getPhrase() . $ip);
        getCache()->set($key, $builder->getPhrase(), $this->ttl);
        return $builder;
    }

    /**
     * @param string $phrase
     * @return bool
     */
    public function verifyCaptcha($phrase)
    {
        if ($phrase) {
            $ip = Request::get()->getRequest()->ip;
            $key = md5($phrase . $ip);
            $v = getCache()->get($key);
            $b = CaptchaBuilder::create($v);
            if ($b->testPhrase($phrase)) {
                return true;
            }
        }
        return false;
    }
}