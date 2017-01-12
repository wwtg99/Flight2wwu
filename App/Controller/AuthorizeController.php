<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/15
 * Time: 14:14
 */

namespace Wwtg99\App\Controller;


use Wwtg99\App\Model\Auth\OAuthServerUser;
use Wwtg99\App\Model\Auth\UserFactory;
use Wwtg99\App\Model\Message;
use Wwtg99\Flight2wwu\Component\Controller\BaseController;
use Wwtg99\PgAuth\Auth\IUser;

/**
 * Class AuthorizeController
 *
 * OAuth server depends on wwtg99/pgauth
 *
 * @package Wwtg99\App\Controller
 */
class AuthorizeController extends BaseController
{

    public static function authorize()
    {
        if (self::getRequest()->checkMethod('POST')) {
            $username = self::getRequest()->getInput('username');
            $pwd = self::getRequest()->getInput('password');
            $scope = self::getRequest()->getInput('scope');
            $cid = self::getRequest()->getInput('client_id');
            $state = self::getRequest()->getInput('state');
            $redirect_uri = self::getRequest()->getInput('redirect_uri');
            if (!$cid) {
                $msg = Message::getMessage(1008);
            } elseif (!$redirect_uri) {
                $msg = Message::getMessage(1010);
            } else {
                if (!$username || !$pwd) {
                    $msg = Message::getMessage(21);
                } else {
                    $u = [IUser::FIELD_USER_NAME=>$username, IUser::FIELD_PASSWORD=>$pwd];
                    //generate code
                    $code = getAuth()->getAuth()->getCode($cid, $redirect_uri, $u);
                    if ($code) {
                        $q = ['code'=>$code];
                        if ($state) {
                            $q['state'] = $state;
                        }
                        $uri = self::createUri($redirect_uri, $q);
                        \Flight::redirect($uri);
                        return false;
                    } else {
                        $msg = Message::getMessage(21, getAuth()->getAuth()->getMessage(), 'danger');
                        getOValue()->addOldOnce('msg', $msg);
                    }
                }
            }
            getOValue()->addOldOnce('msg', $msg);
            $q = [
                'response_type'=>'code',
                'client_id'=>$cid,
                'redirect_uri'=>$redirect_uri
            ];
            if ($state) {
                $q['state'] = $state;
            }
            if ($scope) {
                $q['scope'] = $scope;
            }
            $uri = '/authorize/authorize';
            $uri = self::createUri($uri, $q);
            \Flight::redirect($uri);
            return false;
        } else {
            $rtype = self::getRequest()->getInput('response_type');
            $cid = self::getRequest()->getInput('client_id');
            $rurl = self::getRequest()->getInput('redirect_uri');
            $state = self::getRequest()->getInput('state');
            $scope = self::getRequest()->getInput('scope');
            if (!$rtype || $rtype != 'code') {
                $redata = Message::messageList(1004)->toApiArray();
            } elseif (!$cid) {
                $redata = Message::messageList(1008)->toApiArray();
            } elseif (!$rurl) {
                $redata = Message::messageList(1010)->toApiArray();
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
                    $redata = Message::messageList(1005)->toApiArray();
                }
            }
            return self::getResponse()->setHeader(DefaultController::$defaultViewHeaders)->setResType('view')->setView('oauth/login')->setData(TA($redata))->send();
        }
    }

    public static function token()
    {
        $gtype = self::getRequest()->getInput('grant_type', 'authorization_code');
        $cset = self::getRequest()->getInput('client_secret');
        $code = self::getRequest()->getInput('code');
        $rurl = self::getRequest()->getInput('redirect_uri');
        $state = self::getRequest()->getInput('state');
        if (!$gtype || $gtype != 'authorization_code') {
            $redata = Message::messageList(1006)->toApiArray();
        } elseif (!$cset) {
            $redata = Message::messageList(1011)->toApiArray();
        } elseif (!$rurl) {
            $redata = Message::messageList(1010)->toApiArray();
        } elseif (!$code) {
            $redata = Message::messageList(1002)->toApiArray();
        } else {
            //verify code
            $u = [UserFactory::KEY_CODE=>$code, UserFactory::KEY_APP_SECRET=>$cset];
            $user = getAuth()->login($u);
            if ($user) {
                $us = $user->getUser();
                if (isset($us[IUser::FIELD_TOKEN])) {
                    $token = $us[IUser::FIELD_TOKEN];
                    $ttl = getConfig()->get('auth.token_ttl');
                    $redata = ['access_token'=>$token, 'expires_in'=>time() + $ttl];
                    if ($state) {
                        $redata['state'] = $state;
                    }
                } else {
                    $redata = New Message(21, getAuth()->getAuth()->getMessage(), 'danger');
                    $redata = $redata->toApiArray();
                }
            } else {
                $redata = Message::messageList(1002)->toApiArray();
            }
        }
        self::getResponse()->setHeader(DefaultController::$defaultApiHeaders)->setResType('json')->setData(TA($redata))->send();
        return false;
    }
//
//    public static function user()
//    {
//        $cid = self::getRequest()->getInput('client_id');
//        $token = self::getRequest()->getInput('access_token');
//        if (!$cid) {
//            $redata = Message::messageList(1009)->toApiArray();
//        } elseif (!$token) {
//            $redata = Message::messageList(1012)->toApiArray();
//        }  else {
//            //OAuth server
//            $u = new OAuthServerUser(null);
//            $user = [IUser::FIELD_TOKEN=>$token, UserFactory::KEY_APP_ID=>$cid];
//            $re = $u->verify($user);
//            if ($re) {
//                $redata = $u->getUser()->getUser();
//            } else {
//                $redata = Message::messageList(1012)->toApiArray();
//            }
//        }
//        self::getResponse()->setHeader(DefaultController::$defaultViewHeaders)->setResType('json')->setData(TA($redata))->send();
//        return false;
//    }

    /**
     * @param $uri
     * @param array $query
     * @return string
     */
    private static function createUri($uri, $query = [])
    {
        $p = [];
        foreach ($query as $k => $v) {
            array_push($p, "$k=$v");
        }
        return $uri . '?' . implode('&', $p);
    }
}