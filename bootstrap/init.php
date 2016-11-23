<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/2
 * Time: 10:57
 */

//ini_set('display_errors', '0');

// define path
define('ROOT', realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'));  //root path for project
define('APP', ROOT . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR);  //App path in project
define('CONFIG', APP . 'config' . DIRECTORY_SEPARATOR);  //config path
define('WEB', ROOT . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR);  //web document root path
define("STORAGE", ROOT . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR);  //storage path with write accessible
define("TMP", STORAGE . 'tmp' . DIRECTORY_SEPARATOR);  //tmp path in storage

// autoload
require_once implode(DIRECTORY_SEPARATOR, [ROOT, 'vendor', 'autoload.php']);

// config
$conf_files = ['register_config.php', 'auth.php', 'plugins.json', 'ui_libs.php', 'utils_conf.json', 'app_config.php'];
// no config cache, can be used for debug
$conf = new \Wwtg99\Config\Common\ConfigPool();
// use config cache, merge config at first time
//$conf = new \Wwtg99\Config\Common\ConfigPool(TMP . 'config.cache');
// config source
$source = new \Wwtg99\Config\Source\FileSource(CONFIG, $conf_files);
$source->addLoader(new \Wwtg99\Config\Source\Loader\JsonLoader())->addLoader(new \Wwtg99\Config\Source\Loader\PHPLoader());
$conf->addSource($source);
$conf->load();
Flight::set('config', $conf);  //use Flight::get('config') to get config

require_once 'helpfunctions.php';

// register
$register = \Wwtg99\Flight2wwu\Common\Register::getInstance();
$register->registerAll($conf);

// routes
Flight::Router()->registerRoutes();
