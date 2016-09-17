<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/21
 * Time: 13:26
 */

namespace Wwtg99\Flight2wwu\Component\Utils;

use Wwtg99\StructureFile\Utils\FileHelper;

class FormatUtils
{

    /**
     * Create random string.
     *
     * @param int $length
     * @param string $strPool
     * @return string
     */
    public static function randStr($length, $strPool = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz")
    {
        $strarr = [];
        $max = strlen($strPool) - 1;
        for($i = 0; $i < $length; $i++){
            array_push($strarr, $strPool[mt_rand(0, $max)]);
        }
        return implode('', $strarr);
    }

    /**
     * Format path without last separator.
     *
     * @param string $path
     * @return string
     */
    public static function formatPath($path)
    {
        return FileHelper::formatPath($path);
    }

    /**
     * @param array $paths
     * @return string
     */
    public static function formatPathArray(array $paths)
    {
        return FileHelper::joinPathArray($paths);
    }

    /**
     * Format web path.
     *
     * @param string $path
     * @return string
     */
    public static function formatWebPath($path)
    {
        return (trim($path) && $path != '/') ? '/' . (trim(trim($path), '/')) : '/';
    }

    /**
     * @param array $paths
     * @return string
     */
    public static function formatWebPathArray(array $paths)
    {
        $arr = [];
        for ($i = 0; $i < count($paths); $i++) {
            $p = self::formatWebPath($paths[$i]);
            if ($p == '/') {
                continue;
            } else {
                array_push($arr, $p);
            }
        }
        return implode('', $arr);
    }

    /**
     * Format extension without first dot.
     *
     * @param string $extension
     * @return string
     */
    public static function formatExtension($extension)
    {
        return FileHelper::formatExtension($extension);
    }

    /**
     * Skip array null values and skip keys.
     *
     * @param array $arr
     * @param array $skipKeys
     * @return array
     */
    public static function formatArray(array $arr, $skipKeys = [])
    {
        $out = [];
        foreach ($arr as $k => $v) {
            if (is_null($v)) {
                continue;
            }
            if ($skipKeys && is_array($skipKeys)) {
                if (in_array($k, $skipKeys)) {
                    continue;
                }
            }
            $out[$k] = $v;
        }
        return $out;
    }

    /**
     * @param array $arr
     * @param string $charlist
     * @return array
     */
    public static function trimArray(array $arr, $charlist = " \t\n\r\0\x0B")
    {
        $keys = array_keys($arr);
        for ($i = 0; $i < count($keys); $i++) {
            $arr[$keys[$i]] = trim($arr[$keys[$i]], $charlist);
        }
        return $arr;
    }

    /**
     * Format array to string.
     *
     * @param array $arr
     * @return string
     */
    public static function arrayToString(array $arr)
    {
        $str = [];
        foreach ($arr as $k => $v) {
            array_push($str, "$k = $v");
        }
        return implode(',', $str);
    }

    /**
     * Format time with format.
     *
     * @param string $datetime
     * @param string $format
     * @return string
     */
    public static function formatTime($datetime = null, $format = 'Y-m-d H:i:s')
    {
        if ($datetime) {
            $dt = new \DateTime($datetime);
        } else {
            $dt = new \DateTime();
        }
        return $dt->format($format);
    }
//
//    /**
//     * @param $val
//     * @return string
//     */
//    public static function getExcelDate($val)
//    {
//        if (!$val) {
//            return null;
//        }
//        $jd = GregorianToJD(1, 1, 1970);
//        $gregorian = JDToGregorian($jd + intval($val) - 25569);
//        $darr = date_parse_from_format('m/d/Y', $gregorian);
//        $d = $darr['year'] . '/' . $darr['month'] . '/' . $darr['day'];
//        return $d;
//    }

    /**
     * Format head array to format header
     *
     * @param array $head
     * @return array
     */
    public static function formatHead(array $head) {
        $out = [];
        foreach ($head as $h) {
            array_push($out, ['field'=>$h, 'title'=>T($h)]);
        }
        return $out;
    }

    /**
     * Same as pathinfo but support chinese for < php5.6.
     *
     * @param $filepath
     * @return array
     */
    public static function pathInfo($filepath)
    {
        $p = DIRECTORY_SEPARATOR;
        $f = rtrim($filepath, $p);
        $path_parts = array();
        $path_parts['dirname'] = (strrpos($f, $p) === false ? '.' : substr($f, 0, strrpos($f, $p)));
        if (strrchr($path_parts['dirname'], ':') == ':' || !$path_parts['dirname']) {
            $path_parts['dirname'] .= $p;
        }
        $path_parts['basename'] = ltrim(substr($f, strrpos($f, $p)), $p);
        if (strrchr($filepath, '.') !== false) {
            $path_parts['extension'] = substr(strrchr($f, '.'), 1);
            $path_parts['filename'] = ltrim(substr($path_parts['basename'], 0, strrpos($path_parts['basename'], '.')), $p);
        } else {
            $path_parts['filename'] = $path_parts['basename'];
        }
        return $path_parts;
    }
} 