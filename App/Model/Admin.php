<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/16
 * Time: 15:05
 */

namespace App\Model;


use Flight2wwu\Common\PluginManager;

class Admin {

    public static function plugins()
    {
        $plugins = PluginManager::getInstance()->getPluginConfig();
        return $plugins;
    }
} 