<#php
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
Flight::set('config', $conf);

// load class
$register_path = [
    ['Wwtg99\\App', 'App', true],
];
$loader = new \Wwtg99\ClassLoader\Loader(ROOT, $register_path);
$loader->autoload();

require_once 'helpfunctions.php';

// register
$register = \Wwtg99\Flight2wwu\Common\Register::getInstance();
$register->registerAll($conf);
