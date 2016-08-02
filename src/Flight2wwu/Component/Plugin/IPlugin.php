<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/1/20
 * Time: 10:42
 */

namespace Wwtg99\Flight2wwu\Component\Plugin;


interface IPlugin
{
    /**
     * @return string
     */
    public function getVersion();

    /**
     * Run with parameters
     *
     * @return mixed
     */
    public function run();

    /**
     * Execute command
     *
     * @return mixed
     */
    public function exec();
} 