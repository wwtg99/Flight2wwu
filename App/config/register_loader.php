<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/3
 * Time: 17:17
 */

// Register classes
// [prefix, path, <recursive>, ...]
// prefix for namespace
// path is relative to project root
// recursive default is false, true to register all subdirectories with first letter uppercase

return [
    ['Flight2wwu', 'src' . DIRECTORY_SEPARATOR . 'Flight2wwu', true],
    ['App\Controller', 'App' . DIRECTORY_SEPARATOR . 'Controller', true],
    ['App\Model', 'App' . DIRECTORY_SEPARATOR . 'Model', true],
    ['App\Plugin', 'App' . DIRECTORY_SEPARATOR . 'Plugin', true],
    ['App\Schedule', 'App' . DIRECTORY_SEPARATOR . 'Schedule', true],
];