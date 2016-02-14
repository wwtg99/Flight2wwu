<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/8
 * Time: 15:54
 */

class ComponentTester extends PHPUnit_Framework_TestCase
{

    public static function setUpBeforeClass()
    {
        require '../bootstrap/init.php';
    }

    public function testTrans()
    {
        $trans = new \Flight2wwu\Component\Translation\SymTrans();
        $trans->boot();
        $trans->addResource('zh_CN', 'messages');
        $re = $trans->trans('page not found');
        $this->assertEquals('您访问的页面不存在！', $re);
        $re = $trans->transi('not login');
        $this->assertEquals('您尚未登录！', $re);
        $re = $trans->transi('Not Login');
        $this->assertEquals('您尚未登录！', $re);
        $data = ['a'=>'page not found', 'b'=>['c'=>'not login', 'd'=>'login failed']];
        $re = $trans->transArray($data);
        $this->assertEquals('登录失败！', $re['b']['d']);
        $trans->setLocale('en_AM');
        $re = $trans->trans('login failed');
        $this->assertEquals('Fail to login!', $re);
    }

    public function testValidation()
    {
        $b = Respect\Validation\Validator::numeric()->validate('123');
        $this->assertTrue($b);
        $v = Respect\Validation\Validator::string()->regex('/a.g/');
        $arrv = Respect\Validation\Validator::key('name', $v);
        $b = $arrv->validate(['name'=>'avg']);
        $this->assertTrue($b);
        $b = $arrv->validate(['name'=>'sef']);
        $this->assertFalse($b);
        $b = $arrv->validate(['value'=>'asg']);
        $this->assertFalse($b);
    }

    public function testMarkdown()
    {
        $pd = new Parsedown();
        echo $pd->text("#h1\n##h2");
    }

    public function testCache()
    {
        $cache = new \Flight2wwu\Component\Session\Cache();
        $cache->register();
        $cache->boot();
        $data = ['aa'=>'aaa', 'k1'=>'ggg', 'k2'=>['ga', 'ha']];
        foreach ($data as $k => $v) {
            $key = $cache->generateKey($k);
            $this->assertTrue($cache->store($key, $v));
            $key = $cache->generateKey($k);
            $gv = $cache->retrieve($key);
            $this->assertEquals($v, $gv);
        }
        $k1 = 'key1';
        $v1 = 10;
        $key = $cache->generateKey($k1);
        $this->assertTrue($cache->store($key, $v1));
        $gv = $cache->getAndModify($key, function($value){
            $value += 1;
            return $value;
        });
        $this->assertEquals($v1 + 1, $gv);
        $this->assertEquals($v1 + 1, $cache->retrieve($key));
        $cache->invalidate($key);
        $this->assertFalse($cache->retrieve($key));
    }

    public function testGuzzle()
    {
        $headers = ['Cookie'=>'USER_TOKEN=VTAwMDAwMjU7OjoxOzE0NTIxMzYzNzc%3D'];
//        $headers = [];
        $client = new \GuzzleHttp\Client(['base_uri' => 'http://localhost:8070', 'headers'=>$headers]);
        $res = $client->get('/view');
        $body = $res->getBody();
        echo $body;
    }
}
 