<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/15
 * Time: 14:14
 */

namespace Wwtg99\App\Controller;


use Wwtg99\App\Model\Auth\UserFactory;
use Wwtg99\App\Model\Message;
use Wwtg99\Flight2wwu\Common\BaseController;
use Wwtg99\Flight2wwu\Component\Utils\FormatUtils;

/**
 * Class AuthorizeController
 *
 * OAuth server depends on wwtg99/pgauth
 *
 * @package Wwtg99\App\Controller
 */
class AuthorizeController extends BaseController
{

    const CODE_EXPIRES = 180;

    const TOKEN_EXPIRES = 7200;

    public static function authorize()
    {
        if (self::checkMethod('POST')) {
            $username = self::getInput('username');
            $pwd = self::getInput('password');
            $scope = self::getInput('scope');
            $cid = self::getInput('client_id');
            $state = self::getInput('state');
            $redirect_uri = self::getInput('redirect_uri');
            if (!$cid) {
                $redata = ['error'=>Message::messageList(1008)->toArray()];
            } elseif (!$redirect_uri) {
                $redata = ['error'=>Message::messageList(1010)->toArray()];
            } else {
                $appmodel = getDataPool()->getConnection('auth')->getMapper('App');
                $app = $appmodel->getApp($cid, $redirect_uri);
                if ($app) {
                    if (!$username || !$pwd) {
                        $redata = ['error'=>Message::messageList(21)->toArray()];
                    } else {
                        $u = UserFactory::getUser();
                        $re = $u->verify([UserFactory::KEY_USER_NAME=>$username, UserFactory::KEY_USER_PASSWORD=>$pwd]);
                        if ($re) {
                            //generate code
                            $code = self::generateCode($cid);
                            getCache()->set($code, json_encode($u->getUser()), self::CODE_EXPIRES);
                            $re = ['code=' . $code];
                            if ($state) {
                                array_push($re, 'state=' . $state);
                            }
                            $uri = $redirect_uri . '?' . implode('&', $re);
                            \Flight::redirect($uri);
                            return false;
                        } else {
                            $redata = ['error'=>Message::messageList(21)->toArray()];
                        }
                    }
                    $redata['app'] = $app;
                    $redata['redirect_uri'] = $redirect_uri;
                    if ($state) {
                        $redata['state'] = $state;
                    }
                    if ($scope) {
                        $redata['scope'] = $scope;
                    }
                } else {
                    $redata = ['error'=>Message::messageList(1005)->toArray()];
                }
            }
            getView()->render('oauth/login', $redata);
        } else {
            $rtype = self::getInput('response_type');
            $cid = self::getInput('client_id');
            $rurl = self::getInput('redirect_uri');
            $state = self::getInput('state');
            $scope = self::getInput('scope');
            if (!$rtype || $rtype != 'code') {
                $redata = ['error'=>Message::messageList(1004)->toArray()];
            } elseif (!$cid) {
                $redata = ['error'=>Message::messageList(1008)->toArray()];
            } elseif (!$rurl) {
                $redata = ['error'=>Message::messageList(1010)->toArray()];
            } else {
                $appmodel = getDataPool()->getConnection('auth')->getMapper('App');
                $app = $appmodel->getApp($cid, $rurl);
                if ($app) {
                    $redata = ['app'=>$app, 'redirect_uri'=>$rurl];
                    if ($state) {
                        $redata['state'] = $state;
                    }
                    if ($scope) {
                        $redata['scope'] = $scope;
                    }
                } else {
                    $redata = ['error'=>Message::messageList(1005)->toArray()];
                }
            }
            getView()->render('oauth/login', $redata);
        }
        return false;
    }

    public static function token()
    {
        $gtype = self::getInput('grant_type', 'authorization_code');
        $cid = self::getInput('client_id');
        $cset = self::getInput('client_secret');
        $code = self::getInput('code');
        $rurl = self::getInput('redirect_uri');
        $state = self::getInput('state');
        if (!$gtype || $gtype != 'authorization_code') {
            $redata = ['error'=>Message::messageList(1006)->toArray()];
        } elseif (!$cid) {
            $redata = ['error'=>Message::messageList(1008)->toArray()];
        } elseif (!$cset) {
            $redata = ['error'=>Message::messageList(1011)->toArray()];
        } elseif (!$rurl) {
            $redata = ['error'=>Message::messageList(1010)->toArray()];
        } elseif (!$code) {
            $redata = ['error'=>Message::messageList(1002)->toArray()];
        } else {
            //verify code
            $u = getCache()->get($code);
            if ($u) {
                //verify app
                $appmodel = getDataPool()->getConnection('auth')->getMapper('App');
                $re = $appmodel->verifySecret($cid, $cset, $rurl);
                if (!$re) {
                    $redata = ['error'=>Message::messageList(1007)->toArray()];
                } else {
                    //generate token
                    $token = self::generateToken($cid);
                    $redata = ['access_token'=>$token, 'expires_in'=>self::TOKEN_EXPIRES];
                    if ($state) {
                        $redata['state'] = $state;
                    }
                    //store token in redis
                    getRedis()->set($token, $u, 'EX', self::TOKEN_EXPIRES);
                }

            } else {
                $redata = ['error'=>Message::messageList(1002)->toArray()];
            }
        }
        \Flight::json($redata);
        return false;
    }

    public static function user()
    {
        $cid = self::getInput('client_id');
        $token = self::getInput('access_token');
        $appmodel = getDataPool()->getConnection('auth')->getMapper('App');
        $app = $appmodel->get($cid);
        if ($cid && $app) {
            $u = getRedis()->get($token);
            if ($u) {
                $redata = json_decode($u, true);
            } else {
                $redata = ['error'=>Message::messageList(1012)->toArray()];
            }
        } else {
            $redata = ['error'=>Message::messageList(1009)->toArray()];
        }
        \Flight::json($redata);
        return false;
    }

    /**
     * @param string $app_id
     * @return string
     */
    private static function generateCode($app_id = '')
    {
        $str = $app_id . FormatUtils::randStr(20) . time();
        return substr(md5($str), mt_rand(0, 5), 10);
    }

    /**
     * @param string $app_id
     * @return string
     */
    private static function generateToken($app_id)
    {
        $str = $app_id . FormatUtils::randStr(30) . time();
        return substr(md5($str), 16);
    }
}