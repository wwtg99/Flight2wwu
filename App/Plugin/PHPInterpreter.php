<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/4
 * Time: 11:22
 */

namespace App\Plugin;


use Flight2wwu\Common\IPlugin;

class PHPInterpreter implements IPlugin
{

    /**
     * @var string
     */
    private $path = 'php';

    /**
     * @return string
     */
    public function getVersion()
    {
        $re = $this->exec('-v');
        return $re[count($re) - 1];
    }

    /**
     * Run with parameters
     *
     * @return mixed
     */
    public function run()
    {

    }

    /**
     * Execute command
     *
     * @return mixed
     */
    public function exec()
    {
        $args = func_get_args();
        $op = $args[0];
        if (count($args) > 1) {
            $code = $args[1];
        } else {
            $code = '';
        }
        $out = [];
        $re = exec("$this->path $op $code", $out);
        return $out;
    }

    /**
     * Called after register.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Called after all class is registered.
     *
     * @return void
     */
    public function boot()
    {

    }

} 