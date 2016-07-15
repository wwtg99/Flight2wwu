<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/2
 * Time: 10:57
 */

//ini_set('display_errors', '0');

// define path
define('ROOT', realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'));
define('APP', ROOT . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR);
define('CONFIG', APP . 'config' . DIRECTORY_SEPARATOR);
define('WEB', ROOT . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR);
define("STORAGE", ROOT . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR);
define("TMP", STORAGE . 'tmp' . DIRECTORY_SEPARATOR);
define('WEBROOT', '/');

//config
$conf_files = ['app_config.php', 'register_config.php', 'plugins.json', 'schedule.json', 'ui_libs.php'];
$conf = new \Flight2wwu\Component\Config\FileConfig([CONFIG], $conf_files, true);
Flight::set('config', $conf);

// autoload
$loader = new \ClassLoader\Loader(ROOT, $conf->getConfig('load_path'));
$loader->autoload();

require implode(DIRECTORY_SEPARATOR, [ROOT, 'vendor', 'autoload.php']);
require 'autoload.php';
require 'helpfunctions.php';

// config files
$app_conf = CONFIG . 'app_config.php';

// register
$register = \Flight2wwu\Common\Register::getInstance();
$register->registerAll();
