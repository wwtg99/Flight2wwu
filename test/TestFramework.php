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
}
