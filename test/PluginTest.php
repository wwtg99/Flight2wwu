<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/4
 * Time: 10:52
 */

class PluginTest extends PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass()
    {
        require '../bootstrap/init.php';
    }

    public function testPlugin()
    {
        $p = getPlugin('php');
        $v = $p->getVersion();
        echo "Version: $v\n";
        $a = $p->exec('-r', "\"echo 'haha';\"");
        $this->assertEquals(['haha'], $a);
        \Flight2wwu\Common\PluginManager::getInstance()->disable('1');
        $p = \Flight2wwu\Common\PluginManager::getInstance()->getPlugin('php');
        $this->assertNull($p);
        \Flight2wwu\Common\PluginManager::getInstance()->enable('1');
        \Flight2wwu\Common\PluginManager::getInstance()->writeConfig();
    }
}
 