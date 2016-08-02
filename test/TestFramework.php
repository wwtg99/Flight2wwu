<?php

/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/8/1
 * Time: 11:06
 */
class TestFramework extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        require '../vendor/autoload.php';
//        require '../bootstrap/init.php';
    }

    public function testAssetsManager()
    {
        $conf = [
            'resource_dir'=>'img',
            'global_before'=>['lib1'],
            'global_after'=>['lib2'],
            'libs'=>[
                'lib1'=>[
                    'depends'=>[],
                    'prefix'=>'/lib1',
                    'css'=>['l1.css'],
                    'js'=>['l1.js']
                ],
                'lib2'=>[
                    'depends'=>['lib3'],
                    'prefix'=>'',
                    'css'=>['l2.css', 'll2.css'],
                    'js'=>['l2.js', ['file'=>'ll2.js', 'attr'=>['async'=>true]]]
                ],
                'lib3'=>[
                    'depends'=>'',
                    'prefix'=>'lib3',
                    'css'=>['l3.css', ['file'=>'ll3.css', 'attr'=>['type'=>'text/plain']]],
                    'js'=>['l3.js']
                ],
                'lib4'=>[
                    'depends'=>[],
                    'prefix'=>'',
                    'css'=>['l4.css'],
                    'js'=>[]
                ],
            ]
        ];
        $am = new \Wwtg99\Flight2wwu\Component\View\AssetsManager($conf);
        $css = $am->renderCss();
        $exp1 = <<<STR
<link rel="stylesheet" href="/lib1/l1.css" type="text/css" />
<link rel="stylesheet" href="lib3/l3.css" type="text/css" />
<link type="text/plain" rel="stylesheet" href="lib3/ll3.css" />
<link rel="stylesheet" href="l2.css" type="text/css" />
<link rel="stylesheet" href="ll2.css" type="text/css" />

STR;
        $this->assertEquals($exp1, $css);
        $js = $am->renderJs();
        $exp2 = <<<STR
<script src="/lib1/l1.js" type="text/javascript" ></script>
<script src="lib3/l3.js" type="text/javascript" ></script>
<script src="l2.js" type="text/javascript" ></script>
<script async="1" src="ll2.js" type="text/javascript" ></script>

STR;
        $this->assertEquals($exp2, $js);
        $am->addLibrary('lib4');
        $css = $am->renderCss();
        $exp3 = <<<STR
<link rel="stylesheet" href="/lib1/l1.css" type="text/css" />
<link rel="stylesheet" href="l4.css" type="text/css" />
<link rel="stylesheet" href="lib3/l3.css" type="text/css" />
<link type="text/plain" rel="stylesheet" href="lib3/ll3.css" />
<link rel="stylesheet" href="l2.css" type="text/css" />
<link rel="stylesheet" href="ll2.css" type="text/css" />

STR;
        $this->assertEquals($exp3, $css);
        $js = $am->renderJs();
        $exp4 = <<<STR
<script src="/lib1/l1.js" type="text/javascript" ></script>
<script src="lib3/l3.js" type="text/javascript" ></script>
<script src="l2.js" type="text/javascript" ></script>
<script async="1" src="ll2.js" type="text/javascript" ></script>

STR;
        $this->assertEquals($exp4, $js);
        $this->assertEquals('img/1.png', $am->getResource('1.png'));
        $this->assertEquals('a/2.png', $am->getResource('2.png', 'a'));
        $this->assertEquals('/b/3.png', $am->getResource('3.png', '/b'));
    }

    public function testCache()
    {
        $conf = ['cache'=>['adapter'=>'File', 'params'=>['cache_dir'=>__DIR__ . DIRECTORY_SEPARATOR . 'cache', 'ttl'=>3600]]];
        $cache = new \Wwtg99\Flight2wwu\Component\Storage\Cache($conf);
        $this->assertFalse($cache->has('a'));
        $cache->set('a', 'val1');
        $this->assertTrue($cache->has('a'));
        $this->assertEquals('val1', $cache->get('a'));
        $cache->delete('a');
        $this->assertFalse($cache->has('a'));
        $this->assertNull($cache->get('a'));
        $cache->set('b', 'val2', 1);
        $this->assertTrue($cache->has('b'));
        sleep(2);
        $this->assertFalse($cache->has('b'));
    }

    public function testLocale()
    {
        $conf = ['language'=>'zh_CN', 'directory'=>'../App/config/lang'];
        $locale = new \Wwtg99\Flight2wwu\Component\Translation\SymTrans($conf);
        $this->assertEquals('确定', $locale->trans('OK'));
        $this->assertEquals('ok', $locale->trans('ok'));
        $this->assertEquals('您尚未登录！', $locale->trans('not login'));
        $this->assertEquals('您尚未登录！', $locale->trans('Not Login', [], true));
        $arr = ['a'=>'OK', 'b'=>'Return', 'c'=>'hhh'];
        $exp = ['a'=>'确定', 'b'=>'返回', 'c'=>'hhh'];
        $this->assertEquals($exp, $locale->transArray($arr));
    }

    public function testPlugin()
    {
        require_once '../App/Plugin/PHPInterpreter.php';
        $conf = [
            '1'=>[
                'name'=>'PHP',
                'server_name'=>'php',
                'category'=>'interpreter',
                'description'=>'',
                'class_name'=>'Wwtg99\\App\\Plugin\\PHPInterpreter',
                'enabled'=>true
            ],
        ];
        $pm = new \Wwtg99\Flight2wwu\Component\Plugin\PluginManager($conf);
        $em = $pm->list_enables();
        $this->assertEquals(['1'], $em);
        $this->assertEquals($conf, $pm->getPluginConfig());
        $this->assertEquals($conf['1'], $pm->getPluginConfig('1'));
        $pconf = [
            'name'=>'PHP2',
            'server_name'=>'php2',
            'category'=>'interpreter',
            'description'=>'',
            'class_name'=>'Wwtg99\\App\\Plugin\\PHPInterpreter',
            'enabled'=>true
        ];
        $pm->setPluginConfig('2', $pconf);
        $this->assertEquals(['1'=>$conf['1'], '2'=>$pconf], $pm->getPluginConfig());
        $p = $pm->getPlugin('php');
        $this->assertTrue($p instanceof \Wwtg99\Flight2wwu\Component\Plugin\IPlugin);
        $pm->enable('2');
        $this->assertEquals(['1', '2'], $pm->list_enables());
        $pm->disable('2');
        $this->assertEquals(['1'], $pm->list_enables());
        $pm->disable_server('php');
        $this->assertEquals([], $pm->list_enables());
    }
}
