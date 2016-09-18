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

    public function testRbac()
    {
        $conf = [
            'rbac'=>[
                'admin'=>['*'=>3],
                'common_user'=>[
                    '*'=>0,
                    '/view/*'=>3,
                    '/tran'=>1
                ],
                'test1'=>[
                    '/tran'=>2,
                    '/view/test/*'=>2
                ]
            ]
        ];
        $rbac = new \Wwtg99\Flight2wwu\Component\Auth\RoleAuth($conf);
        $a = $rbac->getRoleAuth('admin', '*');
        $this->assertEquals(3, $a);
        $a = $rbac->getRoleAuth('common_user', '*');
        $this->assertEquals(0, $a);
        $a = $rbac->getRoleAuth('common_user', '/tran');
        $this->assertEquals(1, $a);
        $a = $rbac->getPathAuth('common_user', '/view/a');
        $this->assertEquals(3, $a);
        $a = $rbac->getPathAuth('common_user', '/vie');
        $this->assertEquals(0, $a);
        $a = $rbac->getRoleAuth('admin', '/v');
        $this->assertEquals(-1, $a);
        $a = $rbac->getRoleAuth('common_user', '/v');
        $this->assertEquals(-1, $a);
        $a = $rbac->getRoleAuth(['admin', 'common_user'], '/v');
        $this->assertEquals(-1, $a);
        $a = $rbac->getRoleAuth('admin', '*');
        $this->assertEquals(3, $a);
        $a = $rbac->getRoleAuth('common_user', '*');
        $this->assertEquals(0, $a);
        $a = $rbac->getRoleAuth(['admin', 'common_user'], '*');
        $this->assertEquals(3, $a);
        $a = $rbac->getPathAuth(['admin', 'common_user'], '/v');
        $this->assertEquals(3, $a);
        $a = $rbac->getPathAuth(['test1', 'common_user'], '/tran');
        $this->assertEquals(3, $a);
        $a = $rbac->getPathAuth(['test1', 'common_user'], '/view/test/a');
        $this->assertEquals(2, $a);
        $a = $rbac->getPathAuth('common_user', '/view/test/a');
        $this->assertEquals(3, $a);
    }

    public function testServices()
    {
        $ins = getAuth();
        $this->assertInstanceOf(\Wwtg99\Flight2wwu\Component\Auth\IAuth::class, $ins);
        $ins = getView();
        $this->assertInstanceOf(\Wwtg99\Flight2wwu\Component\View\IView::class, $ins);
        $ins = getLog();
        $this->assertInstanceOf(\Wwtg99\Flight2wwu\Component\Log\ILog::class, $ins);
//        $ins = getDB();
//        $this->assertInstanceOf(\Wwtg99\Flight2wwu\Component\Database\MedooDB::class, $ins);
//        $ins = getRedis();
//        $this->assertInstanceOf(\Wwtg99\Flight2wwu\Component\Database\PRedis::class, $ins);
//        $ins = getDataPool();
//        $this->assertInstanceOf(\Wwtg99\DataPool\Common\IDataPool::class, $ins);
        $ins = getCache();
        $this->assertInstanceOf(\Wwtg99\Flight2wwu\Component\Storage\Cache::class, $ins);
        $ins = getSession();
        $this->assertInstanceOf(\Wwtg99\Flight2wwu\Component\Storage\SessionUtil::class, $ins);
        $ins = getCookie();
        $this->assertInstanceOf(\Wwtg99\Flight2wwu\Component\Storage\CookieUtil::class, $ins);
        $ins = getOValue();
        $this->assertInstanceOf(\Wwtg99\Flight2wwu\Component\Storage\OldValue::class, $ins);
        $ins = getAssets();
        $this->assertInstanceOf(\Wwtg99\Flight2wwu\Component\View\AssetsManager::class, $ins);
        $ins = getMailer();
        $this->assertInstanceOf(\Wwtg99\Flight2wwu\Component\Utils\Mail::class, $ins);
        $ins = Flight::Express();
        $this->assertInstanceOf(\Wwtg99\Flight2wwu\Component\Utils\Express::class, $ins);
        $ins = getPlugin('php');
        $this->assertInstanceOf(\Wwtg99\Flight2wwu\Component\Plugin\IPlugin::class, $ins);
    }

    public function testAjaxRequest()
    {
        $base_uri = 'http://localhost:8880';
        $req1 = new \Wwtg99\Flight2wwu\Component\Utils\AjaxRequest(['base_uri'=>$base_uri]);
        $res = $req1->get('/ajax', ['a'=>'b']);
        self::assertInstanceOf('\Psr\Http\Message\ResponseInterface', $res);
        $req2 = new \Wwtg99\Flight2wwu\Component\Utils\AjaxRequest(['base_uri'=>$base_uri], 'string');
        $res = $req2->get('/ajax', ['a'=>'b']);
//        echo $res;
        $req3 = new \Wwtg99\Flight2wwu\Component\Utils\AjaxRequest(['base_uri'=>$base_uri], 'json');
        $res = $req3->get('/ajax_json', ['a'=>'b']);
        self::assertTrue($res['ajax']);
        self::assertEquals(['a'=>'b'], $res['get']);
        $res = $req3->post('/ajax_json', ['c'=>'d']);
        self::assertEquals(['c'=>'d'], $res['post']);
        $header = ['User-Agent'=>'bbb'];
        $req3->setHeaders($header);
        $res = $req3->get('/ajax_json');
        self::assertEquals('bbb', $res['header']['HTTP_USER_AGENT']);
        $cookies = [['Name'=>'token', 'Value'=>'aaa', 'Domain'=>'localhost', 'Expires'=>time() + 1000]];
        $req3->setCookies($cookies);
        $res = $req3->get('/ajax_json');
        self::assertEquals(['token'=>'aaa'], $res['cookies']);
    }

}
 