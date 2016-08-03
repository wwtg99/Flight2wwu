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
        'dir:',
        'clean',
        'clear-cache::',
        'm-core::'
    ];
    $p = getopt($opt, $lopt);
    return $p;
}

/**
 * @return string
 */
function getHelp()
{
    return getVersion() . "\nInitialize root directory for Flight2wwu framework.\n\n-d  --dir    Install directory, default current work dir\n--clean    Clear dir before installation (Warning: It will remove all related directories).\n--clear-cache  <cache_file>    Remove cache file.\n--m-core=False    Whether to install module core (default False, True if not specified)\n-v  --version    Show version\n-h  --help    Show Help\n";
}

/**
 * @return string
 */
function getVersion()
{
    return 'wwtinit 0.1.0';
}

/**
 * @param string $dir
 */
function clean($dir)
{
    $rd = ['App', 'bootstrap', 'web', 'storage'];
    foreach ($rd as $item) {
        $re = removeDirectory($dir . DIRECTORY_SEPARATOR . $item);
        if (!$re) {
            echo "Remove directory $item failed!\n";
        }
    }
    echo "Clean directory $dir\n";
}

/**
 * @param string $dir
 * @return bool
 */
function removeDirectory($dir)
{
    foreach (new DirectoryIterator($dir) as $fi) {
        if ($fi->isDot()) {
            continue;
        } elseif ($fi->isFile()) {
            unlink($fi->getRealPath());
        } elseif ($fi->isDir()) {
            removeDirectory($fi->getRealPath());
        }
    }
    return rmdir($dir);
}

/**
 * @param string $dir
 * @param $cache
 * @return int
 */
function clearCache($dir, $cache = null)
{
    if (!$cache) {
        $cache = implode(DIRECTORY_SEPARATOR, [$dir, 'storage', 'tmp', 'config.cache']);
    }
    if (file_exists($cache)) {
        unlink($cache);
        echo "Clear cache file $cache\n";
    }
    return 0;
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
    if (!file_exists($dir) || !is_dir($dir)) {
        echo "Invalid directory $dir\n";
        return 1;
    }
    $pkg = implode(DIRECTORY_SEPARATOR, [$dir, 'vendor', 'wwtg99', 'flight2wwu']);
    if (!file_exists($pkg)) {
        echo "No packages found in vendor, please install wwtg99/flight2wwu first!\n";
        return 1;
    }
    echo "Install into $dir...\n";
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
    echo "Install core module...\n";
    // storage
    $st_dir = $dir . DIRECTORY_SEPARATOR . 'storage';
    if (!file_exists($st_dir)) {
        mkdir($st_dir);
    }
    $tm_dir = $st_dir . DIRECTORY_SEPARATOR . 'tmp';
    if (!file_exists($tm_dir)) {
        mkdir($tm_dir);
    }
    // copy files
    $mfiles = [
        'bootstrap'=>['init.php', 'helpfunctions.php'],
        'App'=>[
            'config'=>[
                'app_config.php',
                'auth.php',
                'handlers.php',
                'plugins.json',
                'register_config.php',
                'ui_libs.php',
                'utils_conf.json',
                'lang'=>['zh_CN'=>['messages.php'], 'en_AM'=>['messages.php']]
            ],
            'Controller'=>[
                'DefaultController.php',
                'HomeController.php',
                'AuthController.php'
            ],
            'Model'=>[
                'Message.php',
                'Auth'=>['User.php']
            ],
            'Plugin'=>['PHPInterpreter.php'],
            'view_twig'=>[
                'layout.twig',
                'home.twig',
                'error'=>['403.twig', '404.twig', '500.twig'],
                'auth'=>['login.twig', 'logout.twig', 'change_pwd.twig']
            ],
            'view'=>[
                'border_layout.php',
                'border_head.php',
                'border_foot.php',
                'border_left.php',
                'border_right.php',
                'home.php',
                'error'=>['403.php', '404.php', '500.php'],
                'auth'=>['login.php', 'logout.php', 'change_pwd.php']
            ]
        ],
    ];
    $re = copyFiles($mfiles, $package_dir, $dir);
    // copy web
    if ($re === 0) {
        $re = copyDir($package_dir . DIRECTORY_SEPARATOR . 'web', $dir);
    }
    return $re;
}

/**
 * Copy specified files.
 *
 * @param array $files
 * @param string $src_dir
 * @param string $des_dir
 * @return int
 */
function copyFiles(array $files, $src_dir, $des_dir)
{
    foreach ($files as $i => $file) {
        if (is_array($file)) {
            $dir = $des_dir . DIRECTORY_SEPARATOR . $i;
            if (!file_exists($dir)) {
                mkdir($dir);
            }
            $b = copyFiles($file, $src_dir . DIRECTORY_SEPARATOR . $i, $dir);
            if ($b !== 0) {
                return 1;
            }
        } else {
            if (file_exists($src_dir . DIRECTORY_SEPARATOR . $file)) {
                $b = copy($src_dir . DIRECTORY_SEPARATOR . $file, $des_dir . DIRECTORY_SEPARATOR . $file);
                if (!$b) {
                    echo "Copy $file failed!\n";
                    return 1;
                }
            } else {
                echo "$file does not exists!\n";
                return 1;
            }
        }
    }
    return 0;
}

/**
 * Copy all files in directory.
 *
 * @param string $src
 * @param string $des
 * @return int
 */
function copyDir($src, $des)
{
    if (!file_exists($src)) {
        echo "Directory $src does not exists!\n";
        return 1;
    }
    $d = basename($src);
    if (!file_exists($des . DIRECTORY_SEPARATOR . $d)) {
        mkdir($des . DIRECTORY_SEPARATOR . $d);
    }
    foreach (new DirectoryIterator($src) as $f) {
        if ($f->isDot()) {
            continue;
        } elseif ($f->isFile()) {
            $sf = $f->getRealPath();
            $b = copy($sf, $des . DIRECTORY_SEPARATOR . $d . DIRECTORY_SEPARATOR . $f->getFilename());
            if (!$b) {
                echo "Copy file $sf failed\n";
                return 1;
            }
        } elseif ($f->isDir()) {
            $b = copyDir($f->getRealPath(), $des . DIRECTORY_SEPARATOR . $d);
            if ($b !== 0) {
                return $b;
            }
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
$dir = realpath(rtrim($dir, DIRECTORY_SEPARATOR));
// clean dir
if (isset($opt['clean'])) {
    clean($dir);
}
if (isset($opt['clear-cache'])) {
    // clear cache
    $f = $opt['clear-cache'];
    $re = clearCache($dir, $f);
} else {
    // install modules
    $modules = [];
    // core
    $m_core = 'core';
    if (isset($opt['m-core'])) {
        if (!boolval($opt['m-core'])) {
            $m_core = '';
        }
    }
    if ($m_core) {
        array_push($modules, $m_core);
    }
    //TODO
    $re = installModules($modules, $dir);
    if ($re === 0) {
        echo "Install successfully!\n";
    }
}
exit($re);
