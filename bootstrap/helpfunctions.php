<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/10
 * Time: 16:10
 */

//Define global functions here

/**
 * @return \Wwtg99\Flight2wwu\Component\Utils\Timer
 */
function getTimer()
{
    return Flight::Timer();
}

/**
 * @return \Wwtg99\Config\Common\IConfig
 */
function getConfig()
{
    return Flight::get('config');
}

/**
 * @return \Wwtg99\Flight2wwu\Component\Utils\Captcha
 */
function getCaptcha()
{
    return Flight::Captcha();
}

/**
 * @return \Wwtg99\Flight2wwu\Component\Utils\CSRFCode
 */
function getCSRF()
{
    return Flight::CSRF();
}

/**
 * @return \Wwtg99\DataPool\Common\IDataPool
 */
function getDataPool()
{
    $d = Flight::DataPool();
    if ($d instanceof \Wwtg99\Flight2wwu\Component\Database\DataPool) {
        return $d->getDataPool();
    }
    return null;
}

/**
 * @param $name
 * @return \Wwtg99\Flight2wwu\Component\Plugin\IPlugin
 * @throws Exception
 */
function getPlugin($name)
{
    $pm = Flight::Plugin();
    if ($pm) {
        $p = $pm->getPlugin($name);
        if ($p instanceof \Wwtg99\Flight2wwu\Component\Plugin\IPlugin) {
            return $p;
        }
    }
    throw new \Wwtg99\Flight2wwu\Common\FWException("plugin $name not exists", 1);
}

/**
 * @return \Wwtg99\Flight2wwu\Component\Database\MedooDB
 */
function getDB()
{
    return Flight::DB();
}

/**
 * @return \Wwtg99\Flight2wwu\Component\Database\PRedis
 */
function getRedis()
{
    return Flight::Redis();
}

/**
 * @return \Wwtg99\Flight2wwu\Component\Log\ILog
 */
function getLog()
{
    $log = Flight::Log();
    if (!$log) {
        $log = new \Wwtg99\Flight2wwu\Component\Log\SdoutLog();
    }
    return $log;
}

/**
 * @return \Wwtg99\Flight2wwu\Component\View\IView
 */
function getView()
{
    return Flight::View();
}

/**
 * @return \Wwtg99\Flight2wwu\Component\Storage\OldValue
 */
function getOValue()
{
    return Flight::Value();
}

/**
 * @param string $name
 * @param string $def
 * @return mixed
 */
function getOld($name, $def = '')
{
    $v = getOValue();
    $c = new \Wwtg99\Flight2wwu\Component\Storage\Collection($v->getOlds());
    if ($c->has($name)) {
        return $v->getOld($name, $def);
    } else {
        return $v->getOldOnce($name, $def);
    }
}

/**
 * @return \Wwtg99\Flight2wwu\Component\View\AssetsManager
 */
function getAssets()
{
    return Flight::Assets();
}

/**
 * @return \Wwtg99\Flight2wwu\Component\Auth\RBACAuth
 */
function getAuth()
{
    return Flight::Auth();
}

/**
 * @param string $key
 * @return array|string
 */
function getUser($key = '')
{
    $u = Flight::Auth()->getUser();
    if ($u) {
        $user = $u->getUserArray();
        if ($key) {
            if ($user && array_key_exists($key, $user)) {
                return $user[$key];
            } else {
                return '';
            }
        }
        return $user;
    }
    return '';
}

/**
 * @return \Wwtg99\Flight2wwu\Component\Storage\Cache
 */
function getCache()
{
    return Flight::Cache();
}

/**
 * @return \Wwtg99\Flight2wwu\Component\Storage\SessionUtil
 */
function getSession()
{
    return Flight::Session();
}

/**
 * @return \Wwtg99\Flight2wwu\Component\Storage\CookieUtil
 */
function getCookie()
{
    return Flight::Cookie();
}

/**
 * @return \Wwtg99\Flight2wwu\Component\Utils\Mail
 */
function getMailer()
{
    return Flight::Mail();
}

/**
 * @return bool
 */
function isDebug()
{
    $d = getConfig()->get('debug', false);
    return $d ? true : false;
}

/**
 * Translate
 *
 * @param string $key
 * @param array $parameters
 * @param string $domain
 * @param string $locale
 * @return string
 */
function T($key, $parameters = [], $domain = 'messages', $locale = null)
{
    $trans = Flight::Locale();
    if ($trans) {
        return $trans->trans($key, $parameters, false, $domain, $locale);
    } else {
        return $key;
    }
}

/**
 * Translate case sensitive
 *
 * @param $key
 * @param array $parameters
 * @param string $domain
 * @param string $locale
 * @return string
 */
function TI($key, $parameters = [], $domain = 'messages', $locale = null)
{
    $trans = Flight::Locale();
    return $trans->trans($key, $parameters, true, $domain, $locale);
}

/**
 * Translate and print
 *
 * @param string $key
 * @param array $parameters
 * @param string $domain
 * @param string $locale
 */
function TP($key, $parameters = [], $domain = 'messages', $locale = null)
{
    echo T($key, $parameters, $domain, $locale);
}

/**
 * Translate case sensitive and print
 *
 * @param string $key
 * @param array $parameters
 * @param string $domain
 * @param string $locale
 */
function TIP($key, $parameters = [], $domain = 'messages', $locale = null)
{
    echo TI($key, $parameters, $domain, $locale);
}


/**
 * Translate array
 *
 * @param array $array
 * @param array $parameters
 * @param string $domain
 * @param string $locale
 * @return array
 */
function TA(array $array, $parameters = [], $domain = 'messages', $locale = null)
{
    $trans = Flight::Locale();
    return $trans->transArray($array, $parameters, false, $domain, $locale);
}

/**
 * Translate array case sensitive
 *
 * @param array $array
 * @param array $parameters
 * @param string $domain
 * @param string $locale
 * @return array
 */
function TAI(array $array, $parameters = [], $domain = 'messages', $locale = null)
{
    $trans = Flight::Locale();
    return $trans->transArray($array, $parameters, true, $domain, $locale);
}

/**
 * Format URL.
 *
 * @param string $url
 * @param string $base
 * @return string
 */
function U($url, $base = '')
{
    if (!$base) {
        $base = Flight::get('config')->get('base_url', '');
    }
    return \Wwtg99\Flight2wwu\Component\Utils\FormatUtils::formatWebPathArray([$base, $url]);
}
