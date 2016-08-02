<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/8
 * Time: 10:43
 */

namespace Wwtg99\Flight2wwu\Component\View;



abstract class AbstractView implements IView
{

    protected $config = [];

    /**
     * AbstractView constructor.
     * @param array $conf
     */
    public function __construct($conf = [])
    {
        if (!$conf) {
            $conf = \Flight::get('config')->get('view');
        }
        $this->loadConfig($conf);
    }

    /**
     * @param array $config
     * @return IView
     */
    protected function loadConfig($config)
    {
        if (is_array($config)) {
            $this->config = $config;
        }
        return $this;
    }
} 