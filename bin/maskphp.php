<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/8/4
 * Time: 11:08
 */

function showHelp()
{
    echo "Mask and unmask php scripts.\nMask App and bootstrap directories in specified directory.\n\n-d dir    search directory\n--mask    mask directory\n--unmask    unmask directory\n";
}

/**
 * @param string $dir
 * @param callable $func
 * @return int
 */
function scanDirectory($dir, $func)
{
    $d = DIRECTORY_SEPARATOR;
    $subdirs = ['App' . $d . 'Controller', 'App' . $d . 'Model', 'App' . $d . 'Plugin', 'bootstrap'];
    foreach ($subdirs as $subdir) {
        $re = searchPhp($dir . $d . $subdir, $func);
        if ($re !== 0) {
            return $re;
        }
    }
    return 0;
}

/**
 * @param string $dir
 * @param callable $func
 * @return int
 */
function searchPhp($dir, $func)
{
    if (!file_exists($dir)) {
        echo "Directory $dir does not exists!\n";
        return 1;
    }
    foreach (new DirectoryIterator($dir) as $f) {
        if ($f->isDot()) {
            continue;
        } elseif ($f->isFile()) {
            $p = $f->getExtension();
            if ($p != 'php') {
                continue;
            }
            $sf = $f->getRealPath();
            $func($sf);
        } elseif ($f->isDir()) {
            $b = searchPhp($f->getRealPath(), $func);
            if ($b !== 0) {
                return $b;
            }
        }
    }
    return 0;
}

/**
 * @param $file
 */
function mask($file)
{
    $c = file_get_contents($file);
    if (substr($c, 0, 5) == '<?php') {
        $cc = '<#php' . substr($c, 5);
        file_put_contents($file, $cc);
    }
}

/**
 * @param $file
 */
function unmask($file)
{
    $c = file_get_contents($file);
    if (substr($c, 0, 5) == '<#php') {
        $cc = '<?php' . substr($c, 5);
        file_put_contents($file, $cc);
    }
}

$opt = getopt('hd:', ['help', 'mask', 'unmask']);
if (isset($opt['h']) || isset($opt['help'])) {
    showHelp();
    exit(0);
}
$dir = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');
if (isset($opt['d'])) {
    $dir = $opt['d'];
}
if (isset($opt['mask'])) {
    echo "Mask *.php files in $dir\n";
    $re = scanDirectory($dir, function ($f) { mask($f); });
    if ($re !== 0) {
        echo "Fail to mask!\n";
    }
} elseif (isset($opt['unmask'])) {
    echo "Unmask *.php files in $dir\n";
    $re = scanDirectory($dir, function ($f) { unmask($f); });
    if ($re !== 0) {
        echo "Fail to unmask!\n";
    }
}
exit($re);
