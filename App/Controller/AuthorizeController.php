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
use Wwtg99\Flight2wwu\Common\BaseController;

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
        if (self::checkMethod('POST')) {
            $username = self::getInput('username');
            $pwd = self::getInput('password');
            $scope = self::getInput('scope');
            $cid = self::getInput('client_id');
            $state = self::getInput('state');
            $redirect_uri = self::getInput('redirect_uri');
            if (!$cid) {
                $msg = Message::getMessage(1008);
            } elseif (!$redirect_uri) {
                $msg = Message::getMessage(1010);
            } else {
                if (!$username || !$pwd) {
                    $msg = Message::getMessage(21);
                } else {
                    $user = [UserFactory::KEY_USER_NAME=>$username, UserFactory::KEY_USER_PASSWORD=>$pwd, UserFactory::KEY_APP_ID=>$cid, UserFactory::KEY_APP_REDIRECT_URI=>$redirect_uri];
                    //OAuth server
                    $u = new OAuthServerUser(null);
                    //generate code
                    $code = $u->getCode($user);
                    if ($code) {
                        $q = ['code'=>$code];
                        if ($state) {
                            $q['state'] = $state;
                        }
                        $uri = self::createUri($redirect_uri, $q);
                        \Flight::redirect($uri);
                        return false;
                    } else {
                        $msg = Message::getMessage(21, $u->getMessage(), 'danger');
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
            $rtype = self::getInput('response_type');
            $cid = self::getInput('client_id');
            $rurl = self::getInput('redirect_uri');
            $state = self::getInput('state');
            $scope = self::getInput('scope');
            if (!$rtype || $rtype != 'code') {
                $redata = ['error'=>Message::getMessage(1004)];
            } elseif (!$cid) {
                $redata = ['error'=>Message::getMessage(1008)];
            } elseif (!$rurl) {
                $redata = ['error'=>Message::getMessage(1010)];
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
                    $redata = ['error'=>Message::getMessage(1005)];
                }
            }
            getView()->render('oauth/login', $redata);
        }
        return false;
    }

    public static function token()
    {
        $gtype = self::getInput('grant_type', 'authorization_code');
        $cset = self::getInput('client_secret');
        $code = self::getInput('code');
        $rurl = self::getInput('redirect_uri');
        $state = self::getInput('state');
        if (!$gtype || $gtype != 'authorization_code') {
            $redata = ['error'=>Message::getMessage(1006)];
        } elseif (!$cset) {
            $redata = ['error'=>Message::getMessage(1011)];
        } elseif (!$rurl) {
            $redata = ['error'=>Message::getMessage(1010)];
        } elseif (!$code) {
            $redata = ['error'=>Message::getMessage(1002)];
        } else {
            //OAuth server
            $u = new OAuthServerUser(null);
            //verify code
            $user = [UserFactory::KEY_CODE=>$code, UserFactory::KEY_APP_SECRET=>$cset];
            $re = $u->login($user);
            if ($re) {
                $us = $u->getUser()->getUser();
                if (isset($us[UserFactory::KEY_USER_TOKEN])) {
                    $token = $us[UserFactory::KEY_USER_TOKEN];
                    $ttl = getConfig()->get('token_ttl');
                    $redata = ['access_token'=>$token, 'expires_in'=>time() + $ttl];
                    if ($state) {
                        $redata['state'] = $state;
                    }
                } else {
                    $redata = ['error'=>Message::getMessage(21, $u->getMessage(), 'danger')];
                }
            } else {
                $redata = ['error'=>Message::getMessage(1002)];
            }
        }
        \Flight::json($redata);
        return false;
    }

    public static function user()
    {
        $cid = self::getInput('client_id');
        $token = self::getInput('access_token');
        if (!$cid) {
            $redata = ['error'=>Message::getMessage(1009)];
        } elseif (!$token) {
            $redata = ['error'=>Message::getMessage(1012)];
        }  else {
            //OAuth server
            $u = new OAuthServerUser(null);
            $user = [UserFactory::KEY_USER_TOKEN=>$token, UserFactory::KEY_APP_ID=>$cid];
            $re = $u->verify($user);
            if ($re) {
                $redata = $u->getUser()->getUser();
            } else {
                $redata = ['error'=>Message::getMessage(1012)];
            }
        }
        \Flight::json(TA($redata));
        return false;
    }

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