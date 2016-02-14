<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/8
 * Time: 19:19
 */

namespace Flight2wwu\Component\Translation;

use Flight2wwu\Common\ServiceProvider;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;

class SymTrans implements ServiceProvider
{

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @var string
     */
    private $path;

    /**
     * Called after register.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Called after all class is registered.
     *
     * @return void
     */
    public function boot()
    {
        $locale = getLValue()->getOld('language');
        if (!$locale) {
            $locale = \Flight::get('language');
        }
        $this->translator = new Translator($locale);
        $this->translator->addLoader('array', new ArrayLoader());
        $this->setLocale($locale);
    }

    function __construct()
    {
        $this->path = CONFIG . 'lang';
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = rtrim(trim($path), DIRECTORY_SEPARATOR);
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->translator->setLocale($locale);
        $this->addResource($locale, 'messages');
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->translator->getLocale();
    }

    /**
     * @param string $locale
     * @param string $domain
     */
    public function addResource($locale, $domain = 'messages')
    {
        $resource = $domain;
        if (substr($domain, -4, 4) != '.php') {
            $resource .= '.php';
        } else {
            $domain = substr($domain, 0, strlen($domain) - 4);
        }
        $file = implode(DIRECTORY_SEPARATOR, [$this->path, $locale, $resource]);
        if (file_exists($file)) {
            $re = include "$file";
            $this->translator->addResource('array', $re, $locale, $domain);
        }
    }

    /**
     * @param string $key
     * @param array $parameters
     * @param string $domain
     * @param string $locale
     * @return string
     */
    public function trans($key, $parameters = [], $domain = 'messages', $locale = null)
    {
        return $this->translator->trans($key, $parameters, $domain, $locale);
    }

    /**
     * @param string $key
     * @param array $parameters
     * @param string $domain
     * @param string $locale
     * @return string
     */
    public function transi($key, $parameters = [], $domain = 'messages', $locale = null)
    {
        $v = $this->trans($key, $parameters, $domain, $locale);
        if ($v == $key) {
            $vv = $this->trans(strtolower($key), $parameters, $domain, $locale);
            if ($vv != strtolower($key)) {
                $v = $vv;
            }
        }
        return $v;
    }

    /**
     * @param array $array
     * @param array $parameters
     * @param string $domain
     * @param string $locale
     * @return array
     */
    public function transArray(array $array, $parameters = [], $domain = 'messages', $locale = null)
    {
        $out = [];
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $out[$k] = $this->transArray($v, $parameters, $domain, $locale);
            } else {
                $out[$k] = $this->trans($v, $parameters, $domain, $locale);
            }
        }
        return $out;
    }

    /**
     * @param array $array
     * @param array $parameters
     * @param string $domain
     * @param string $locale
     * @return array
     */
    public function transArrayi(array $array, $parameters = [], $domain = 'messages', $locale = null)
    {
        $out = [];
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $out[$k] = $this->transArrayi($v, $parameters, $domain, $locale);
            } else {
                $out[$k] = $this->transi($v, $parameters, $domain, $locale);
            }
        }
        return $out;
    }

    /**
     * @param string $locale
     * @return array
     */
    public function getAll($locale = null)
    {
        return $this->translator->getMessages($locale);
    }

} 