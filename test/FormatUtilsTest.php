<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/21
 * Time: 13:29
 */

use Wwtg99\Flight2wwu\Component\Utils\FormatUtils;

class FormatUtilsTest extends PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass()
    {
        require '../vendor/autoload.php';
        require '../src/Flight2wwu/Component/Utils/FormatUtils.php';
//        require '../bootstrap/init.php';
        date_default_timezone_set('Asia/Shanghai');
    }

    public function testFormat() {
        // path
        $paths = ['', DIRECTORY_SEPARATOR, 'aa', 'aa' . DIRECTORY_SEPARATOR, 'aa/bb', DIRECTORY_SEPARATOR . 'cc', '/cc/dd' . DIRECTORY_SEPARATOR];
        $expes = ['', DIRECTORY_SEPARATOR, 'aa', 'aa', 'aa/bb', DIRECTORY_SEPARATOR . 'cc', '/cc/dd'];
        for ($i = 0; $i < count($paths); $i++) {
            $this->assertEquals($expes[$i], FormatUtils::formatPath($paths[$i]));
        }
        $paths = [
            ['a', 'b', 'c'],
            ['a', 'b', 'c.txt'],
            [DIRECTORY_SEPARATOR, 'b', 'c.txt'],
            ['/a', 'b', 'c.txt'],
            ['', 'b', 'c.txt'],
            ['a', '', 'c'],
            ['a', 'b' . DIRECTORY_SEPARATOR, 'c'],
        ];
        $expes = [
            'a' . DIRECTORY_SEPARATOR . 'b' . DIRECTORY_SEPARATOR . 'c',
            'a' . DIRECTORY_SEPARATOR . 'b' . DIRECTORY_SEPARATOR . 'c.txt',
            DIRECTORY_SEPARATOR . 'b' . DIRECTORY_SEPARATOR . 'c.txt',
            '/a' . DIRECTORY_SEPARATOR . 'b' . DIRECTORY_SEPARATOR . 'c.txt',
            'b' . DIRECTORY_SEPARATOR . 'c.txt',
            'a' . DIRECTORY_SEPARATOR . 'c',
            'a' . DIRECTORY_SEPARATOR . 'b' . DIRECTORY_SEPARATOR . 'c'
        ];
        for ($i = 0; $i < count($paths); $i++) {
            $this->assertEquals($expes[$i], FormatUtils::formatPathArray($paths[$i]));
        }
        // web path
        $paths = ['', 'aa', '/aa', 'aa/', '/aa/', 'aa/bb', '/aa/bb/', '/aa/c.html'];
        $expes = ['/', '/aa', '/aa', '/aa', '/aa', '/aa/bb', '/aa/bb', '/aa/c.html'];
        for ($i = 0; $i < count($paths); $i++) {
            $this->assertEquals($expes[$i], FormatUtils::formatWebPath($paths[$i]), $paths[$i]);
        }
        // web path array
        $paths = [
            ['a', 'b', 'c'],
            ['a', 'b', 'c/'],
            ['/b', 'c'],
            ['/a', 'b/', 'c/'],
            ['', 'b', 'c'],
            ['a', '', 'c'],
            ['a', 'b', ''],
            ['a', 'b', 'c.html'],
            ['/', 'b', 'c'],
            ['/', 'b/c'],
        ];
        $expes = [
            '/a/b/c',
            '/a/b/c',
            '/b/c',
            '/a/b/c',
            '/b/c',
            '/a/c',
            '/a/b',
            '/a/b/c.html',
            '/b/c',
            '/b/c',
        ];
        for ($i = 0; $i < count($paths); $i++) {
            $this->assertEquals($expes[$i], FormatUtils::formatWebPathArray($paths[$i]), print_r($paths[$i], true));
        }
        // extension
        $ext = ['xlsx', '.xlsx', 'txt', '.txt', 'as.txt', 'gg.', 'a$g', 'b,e/y'];
        $exp = ['xlsx', 'xlsx', 'txt', 'txt', 'as.txt', 'gg', 'ag', 'bey'];
        for ($i = 0 ; $i < count($ext); $i++) {
            $this->assertEquals($exp[$i], FormatUtils::formatExtension($ext[$i]));
        }
        // format array
        $arr1 = ['a', 'b', 1];
        $exp1 = ['a', 'b', 1];
        $this->assertEquals($exp1, FormatUtils::formatArray($arr1));
        $arr2 = ['a'=>1, 'b'=>'aa', 'c'=>null, 'd'=>'null'];
        $exp2 = ['a'=>1, 'b'=>'aa', 'd'=>'null'];
        $this->assertEquals($exp2, FormatUtils::formatArray($arr2));
        $arr3 = ['a'=>1, 'b'=>'aa', 'c'=>null, 'd'=>'null'];
        $exp3 = ['d'=>'null'];
        $this->assertEquals($exp3, FormatUtils::formatArray($arr3, ['a', 'b']));
        // trim array
        $arr1 = ['a', 'b ', " c\n"];
        $exp1 = ['a', 'b', 'c'];
        $this->assertEquals($exp1, FormatUtils::trimArray($arr1));
        $arr2 = ['a'=>'aa', 'b'=>' bb ', 2=>"\rcc\t"];
        $exp2 = ['a'=>'aa', 'b'=>'bb', 2=>'cc'];
        $this->assertEquals($exp2, FormatUtils::trimArray($arr2));
        // array to string
        $test_a2s = [
            'a = 1'=>['a'=>1],
            'a = 1,b = a'=>['a'=>1, 'b'=>'a'],
        ];
        foreach ($test_a2s as $exp => $t) {
            $this->assertEquals($exp, FormatUtils::arrayToString($t));
        }
        // format time
        $time1 = '2015/01/01 10:12:10';
        $exp1 = '2015-01-01 10:12:10';
        $this->assertEquals($exp1, FormatUtils::formatTime($time1));
        $time2 = '2015-01-01 10:12:20';
        $exp2 = '2015-01-01 10:12:20';
        $this->assertEquals($exp2, FormatUtils::formatTime($time2));
        $time3 = '2015-01-10 10:12:20';
        $exp3 = '2015-01-10';
        $this->assertEquals($exp3, FormatUtils::formatTime($time3, 'Y-m-d'));
    }

    public function atestTrans()
    {
        // format trans array
        $data = ['a'=>'aa', 'b'=>'Return', 'c'=>['ca'=>'OK', 'cb'=>'2015/1/9 10:10:10.666666'], 'created_at'=>'2016-1-8 12:12:12.666666', 'd'=>['deleted_at'=>'2016/1/7 9:9:9']];
        $data = FormatUtils::formatTransArray($data);
        $exp4 = ['a'=>'aa', 'b'=>'返回', 'c'=>['ca'=>'确定', 'cb'=>'2015/1/9 10:10:10.666666'], 'created_at'=>'2016-01-08 12:12:12', 'd'=>['deleted_at'=>'2016-01-07 09:09:09']];
        $this->assertEquals($exp4, $data);
        // format head
        $head = ['a', 'Return', 'b'];
        $exp5 = [['field'=>'a', 'title'=>'a'], ['field'=>'Return', 'title'=>'返回'], ['field'=>'b', 'title'=>'b']];
        $head = FormatUtils::formatHead($head);
        $this->assertEquals($exp5, $head);
    }

    public function testPathinfo()
    {
        $test = ['\home\a\b.txt', 'cc.xml', '\home\aaa', '\bb\cc\\', 'C:\aa\bb', 'C:\downloads', '\dd.tsv'];
        foreach ($test as $t) {
            $this->assertEquals(pathinfo($t), FormatUtils::pathInfo($t));
        }
        $test2 = [
            '文件.txt'=>['dirname'=>'.', 'basename'=>'文件.txt', 'extension'=>'txt', 'filename'=>'文件'],
            'F:\downloads\20150303 网页图片'=>['dirname'=>'F:\downloads', 'basename'=>'20150303 网页图片', 'filename'=>'20150303 网页图片'],
            '\网页图片1.doc'=>['dirname'=>'\\', 'basename'=>'网页图片1.doc', 'extension'=>'doc', 'filename'=>'网页图片1'],
            '\网页图片\中文1.php'=>['dirname'=>'\网页图片', 'basename'=>'中文1.php', 'extension'=>'php', 'filename'=>'中文1'],
        ];
        foreach ($test2 as $t => $e) {
            $pi = FormatUtils::pathInfo($t);
            $this->assertEquals($e, $pi);
        }
    }

}
 