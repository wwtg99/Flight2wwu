<?php

/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/7/15
 * Time: 11:43
 */
class TestConfig extends PHPUnit_Framework_TestCase
{

    public static function setUpBeforeClass()
    {
        require '../vendor/autoload.php';
        require '../bootstrap/init.php';
    }

    public function testLoadConfig()
    {
        $configDirectories = array(APP . 'config');
        $fc1 = new \Flight2wwu\Component\Config\FileConfig($configDirectories, ['plugins.json', 'app_config.php'], false);
        $re1 = $fc1->export();
        $fc2 = new \Flight2wwu\Component\Config\FileConfig([], [], true);
        $re2 = $fc2->export();
        $this->assertEquals($re1, $re2);
        $test1 = ['bb'=>['cc'=>'dd']];
        $fc2->setConfig('aa', $test1);
        $c = $fc2->getConfig('aa');
        $this->assertEquals($test1, $c);
        $c = $fc2->getConfig('aa.bb.cc');
        $this->assertEquals('dd', $c);
    }
}
