<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/8/3
 * Time: 9:30
 */

/**
 * @return array
 */
function parseOption()
{
    $opt = 'hvd:';
    $lopt = [
        'help',
        'version',
        'dir:'
    ];
    $p = getopt($opt, $lopt);
    return $p;
}

/**
 * @return string
 */
function getHelp()
{
    return getVersion() . "\nInitialize root directory for Flight2wwu framework.\n\n-d  --dir    Install directory, default current work dir\n-v  --version    Show version\n-h  --help    Show Help\n";
}

/**
 * @return string
 */
function getVersion()
{
    return 'wwtinit 0.1.0';
}

/**
 * @param array $modules
 * @param string $name
 * @return array
 */
function addModule(&$modules, $name)
{
    return $modules;
}

/**
 * @param array $modules
 * @param string $dir
 * @return int
 */
function installModules($modules, $dir)
{
    $dir = realpath(rtrim($dir, DIRECTORY_SEPARATOR));
    if (!file_exists($dir) || !is_dir($dir)) {
        echo "Invalid directory $dir\n";
        return 1;
    }
    $pkg = implode(DIRECTORY_SEPARATOR, [$dir, 'vendor', 'wwtg99', 'flight2wwu']);
    if (!file_exists($pkg)) {
        echo "No packages found in vendor, please install wwtg99/flight2wwu first!\n";
        return 1;
    }
    foreach ($modules as $module) {
        switch ($module) {
            case 'core': $re = installCore($dir, $pkg); break;
            default: $re = 0;
        }
        if ($re !== 0) {
            echo "Install module $module failed!\n";
            return $re;
        }
    }
    return 0;
}

/**
 * @param string $dir
 * @param string $package_dir
 * @return int
 */
function installCore($dir, $package_dir)
{
    $d = DIRECTORY_SEPARATOR;
    // bootstrap
    $bt_dir = $dir . $d . 'bootstrap';
    if (!file_exists($bt_dir)) {
        mkdir($bt_dir);
    }
    $bfiles = ['init.php', 'helpfunctions.php'];
    foreach ($bfiles as $bfile) {
        $b = copy($package_dir . $d . 'bootstrap' . $d . $bfile, $bt_dir . $d . $bfile);
        if (!$b) {
            return 1;
        }
    }
    // App
    $app_dir = $dir . $d . 'App';
    if (!file_exists($app_dir)) {
        mkdir($app_dir);
    }
    $mfiles = ['config' . $d . 'app_config.php', 'config' . $d . 'auth.php', 'config' . $d . 'handlers.php', 'config' . $d . 'plugins.json', 'config' . $d . 'register_config.php', 'config' . $d . 'ui_libs.php', 'config' . $d . 'utils_conf.json', implode($d, ['config', 'lang', 'zh_CN', 'messages.php']), implode($d, ['config', 'lang', 'en_AM', 'messages.php']), 'Controller' . $d . 'DefaultController.php', 'Controller' . $d . 'HomeController.php', 'Controller' . $d . 'AuthController.php', implode($d, ['Model', 'Auth', 'User.php']), 'Model' . $d . 'Message.php', 'Plugin' . $d . 'PHPInterpreter.php', 'view_twig' . $d . 'layout.twig', 'view_twig' . $d . 'home.twig', implode($d, ['view_twig', 'error', '403.twig']), implode($d, ['view_twig', 'error', '404.twig']), implode($d, ['view_twig', 'error', '500.twig']), implode($d, ['view_twig', 'auth', 'login.twig']), implode($d, ['view_twig', 'auth', 'logout.twig']), implode($d, ['view_twig', 'auth', 'change_pwd.twig']), 'view' . $d . 'border_layout.php', 'view' . $d . 'border_head.php', 'view' . $d . 'border_foot.php', 'view' . $d . 'border_left.php', 'view' . $d . 'border_right.php', 'view' . $d . 'home.php', implode($d, ['view', 'error', '403.php']), implode($d, ['view', 'error', '404.php']), implode($d, ['view', 'error', '500.php']), implode($d, ['view', 'auth', 'login.php']), implode($d, ['view', 'auth', 'logout.php']), implode($d, ['view', 'auth', 'change_pwd.php'])];
    foreach ($mfiles as $mfile) {
        $b = copy($package_dir . $d . 'App' . $d . $mfile, $app_dir . $d . $mfile);
        if (!$b) {
            return 1;
        }
    }
    return 0;
}


$opt = parseOption();
if (isset($opt['v']) || isset($opt['version'])) {
    echo getVersion();
    exit();
} elseif (isset($opt['h']) || isset($opt['help'])) {
    echo getHelp();
    exit();
}
// install directory
$dir = getcwd();
if (isset($opt['d'])) {
    $dir = $opt['d'];
} elseif (isset($opt['dir'])) {
    $dir = $opt['dir'];
}
// install modules
$modules = ['core'];
//TODO
$re = installModules($modules, $dir);
exit($re);
