<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/5/8 0008
 * Time: 下午 8:44
 */

namespace Flight2wwu\Component\View;


use Flight2wwu\Component\Utils\FormatUtils;

class TwigView extends AbstractView
{

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * Called after all class is registered.
     *
     * @return void
     */
    public function boot()
    {
        $view = \Flight::get('view');
        $this->loadConfig($view);
    }

    /**
     * @param array $conf
     */
    public function loadConfig($conf)
    {
        $dir = isset($conf['view_dir']) ? FormatUtils::formatPath($conf['view_dir']) : '';
        $loader = new \Twig_Loader_Filesystem($dir);
        if (isDebug()) {
            $conf['debug'] = true;
        }
        $this->twig = new \Twig_Environment($loader, $conf);
        if (isDebug()) {
            $this->twig->addExtension(new \Twig_Extension_Debug());
        }
    }

    /**
     * @param $template
     * @param array $data
     */
    public function render($template, array $data = [])
    {
        echo $this->twig->render($this->getTemplate($template), $data);
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