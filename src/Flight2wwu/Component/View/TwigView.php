<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/5/8 0008
 * Time: 下午 8:44
 */

namespace Wwtg99\Flight2wwu\Component\View;


use Wwtg99\Flight2wwu\Component\Utils\FormatUtils;

class TwigView extends AbstractView
{

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * TwigView constructor.
     * @param array $conf
     */
    public function __construct($conf = [])
    {
        parent::__construct($conf);
        $this->addExtensions();
    }

    /**
     * @param $template
     * @param array $data
     * @return $this;
     */
    public function render($template, array $data = [])
    {
        echo $this->twig->render($this->getTemplate($template), $data);
        return $this;
    }

    /**
     * @param array $conf
     * @return IView
     */
    protected function loadConfig($conf)
    {
        $dir = isset($conf['view_dir']) ? FormatUtils::formatPath($conf['view_dir']) : '';
        $loader = new \Twig_Loader_Filesystem($dir);
        if (isDebug()) {
            $conf['debug'] = true;
            $this->twig = new \Twig_Environment($loader, $conf);
            $this->twig->addExtension(new \Twig_Extension_Debug());
        } else {
            $this->twig = new \Twig_Environment($loader, $conf);
        }
        return $this;
    }

    /**
     * Add Extensions.
     */
    protected function addExtensions()
    {
        $this->addFunctions();
        $this->addFilters();
    }

    /**
     * Add twig extended filters
     */
    protected function addFilters()
    {
        // Translate
        $filter = new \Twig_SimpleFilter('T', function($val, $param = [], $domain = 'messages', $locale = null) {
            return T($val, $param, $domain, $locale);
        });
        $this->twig->addFilter($filter);
        // Translate case insensitive
        $filter = new \Twig_SimpleFilter('TI', function($val, $param = [], $domain = 'messages', $locale = null) {
            return TI($val, $param, $domain, $locale);
        });
        $this->twig->addFilter($filter);
        // Translate array
        $filter = new \Twig_SimpleFilter('TA', function(array $val, $param = [], $domain = 'messages', $locale = null) {
            return TA($val, $param, $domain, $locale);
        });
        $this->twig->addFilter($filter);
        // Translate array case insensitive
        $filter = new \Twig_SimpleFilter('TAI', function(array $val, $param = [], $domain = 'messages', $locale = null) {
            return TAI($val, $param, $domain, $locale);
        });
        $this->twig->addFilter($filter);
        // json_encode
        $filter = new \Twig_SimpleFilter('J', function($obj, $options = JSON_UNESCAPED_UNICODE) {
            return json_encode($obj, $options);
        }, ['is_safe'=>['html']]);
        $this->twig->addFilter($filter);
        // markdown
        $filter = new \Twig_SimpleFilter('M', function($text) {
            $md = new \Parsedown();
            return $md->text($text);
        }, ['is_safe'=>['html']]);
        $this->twig->addFilter($filter);
        // format url
        $filter = new \Twig_SimpleFilter('U', function($url, $prefix = '') {
            return U($url, $prefix);
        });
        $this->twig->addFilter($filter);
    }

    /**
     * Add twig extended functions
     */
    protected function addFunctions()
    {
        // isDebug
        $func = new \Twig_SimpleFunction('isDebug', function() {
            return isDebug();
        });
        $this->twig->addFunction($func);
        // debugbarHead
        $func = new \Twig_SimpleFunction('debugbarHead', function() {
            $debugbar = \Flight::get('debugbar');
            if ($debugbar) {
                $debugRender = $debugbar->getJavascriptRenderer();
                $debugRender->setBaseUrl(U('assets/debugbar'));
                return $debugRender->renderHead();
            }
            return '';
        }, ['is_safe'=>['html']]);
        $this->twig->addFunction($func);
        // renderDubugbar
        $func = new \Twig_SimpleFunction('renderDebugbar', function() {
            $debugbar = \Flight::get('debugbar');
            if ($debugbar) {
                $debugRender = $debugbar->getJavascriptRenderer();
                return $debugRender->render();
            }
            return '';
        }, ['is_safe'=>['html']]);
        $this->twig->addFunction($func);
        // renderAssets
        $func = new \Twig_SimpleFunction('renderAssets', function($addlib = []) {
            $ass = getAssets();
            if ($addlib) {
                $ass->addLibrary($addlib);
            }
            return $ass->renderCss() . $ass->renderJs();
        }, ['is_safe'=>['html']]);
        $this->twig->addFunction($func);
        // isLogin
        $func = new \Twig_SimpleFunction('isLogin', function() {
            return getAuth()->isLogin();
        });
        $this->twig->addFunction($func);
        // isSuperuser
        $func = new \Twig_SimpleFunction('isSuperuser', function() {
            return getAuth()->isSuperuser();
        });
        $this->twig->addFunction($func);
        // hasRole
        $func = new \Twig_SimpleFunction('hasRole', function($role) {
            return getAuth()->hasRole($role);
        });
        $this->twig->addFunction($func);
        // getConfig
        $func = new \Twig_SimpleFunction('getConfig', function($name) {
            return \Flight::get('config')->get($name);
        });
        $this->twig->addFunction($func);
        // getUser
        $func = new \Twig_SimpleFunction('getUser', function() {
            return getAuth()->getUser();
        });
        $this->twig->addFunction($func);
        // old value
        $func = new \Twig_SimpleFunction('old', function($name, $def = '') {
            return getOld($name, $def);
        });
        $this->twig->addFunction($func);
        // log
        $func = new \Twig_SimpleFunction('log', function($level, $msg, $context = []) {
            return getLog()->log($level, $msg, $context);
        });
        $this->twig->addFunction($func);
    }

    /**
     * @param $template
     * @return string
     */
    private function getTemplate($template)
    {
        $template = trim($template);
        $dpos = strrpos($template, '.');
        if ($dpos === false) {
            return $template . '.twig';
        }
        $suf = substr($template, $dpos);
        if (!in_array($suf, ['.html', '.php', '.twig'])) {
            return $template . '.twig';
        }
        return $template;
    }

}