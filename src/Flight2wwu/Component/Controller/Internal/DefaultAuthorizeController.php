<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2017/2/5
 * Time: 14:32
 */

namespace Wwtg99\Flight2wwu\Component\Controller\Internal;


use Wwtg99\App\Controller\DefaultController;
use Wwtg99\App\Model\Auth\UserFactory;
use Wwtg99\App\Model\Message;
use Wwtg99\Flight2wwu\Component\Controller\BaseController;
use Wwtg99\PgAuth\Auth\IAuth;
use Wwtg99\PgAuth\Auth\IUser;
use Wwtg99\PgAuth\Utils\AppUtils;
use Wwtg99\PgAuth\Utils\OAuthUtils;

class DefaultAuthorizeController extends BaseController
{

    public function authorize()
    {
        session_start();
        if (self::getRequest()->checkMethod('POST')) {
            return $this->postAuthorize();
        } else {
            return $this->getAuthorize();
        }
    }

    public function token()
    {
        $gtype = self::getRequest()->getInput('grant_type', 'authorization_code');
        $cset = self::getRequest()->getInput('client_secret');
        $code = self::getRequest()->getInput('code');
        $state = self::getRequest()->getInput('state');
        if (!$gtype || $gtype != 'authorization_code') {
            $msg = Message::messageList(1006);
            $redata = $msg->toApiArray();
        } elseif (!$cset) {
            $msg = Message::messageList(1011);
            $redata = $msg->toApiArray();
        } elseif (!$code) {
            $msg = Message::messageList(1002);
            $redata = $msg->toApiArray();
        } else {
            //verify code
            $oauth = new OAuthUtils(getConfig()->get('auth'));
            $appu = new AppUtils(getDataPool()->getConnection('auth'));
            $user = $oauth->verifyCode($code);
            if ($user) {
                $cid = $user[AppUtils::FIELD_APP_ID];
                if ($appu->verifySecret($cid, $cset)) {
                    if (isset($user[IUser::FIELD_TOKEN])) {
                        $token = $user[IUser::FIELD_TOKEN];
                        $ttl = getConfig()->get('auth.token_ttl');
                        $redata = ['access_token'=>$token, 'expires_in'=>time() + $ttl];
                        if ($state) {
                            $redata['state'] = $state;
                        }
                    } else {
                        $msg = Message::messageList(1001);
                        $redata = $msg->toApiArray();
                    }
                } else {
                    $msg = Message::messageList(1007);
                    $redata = $msg->toApiArray();
                }
            } else {
                $msg = Message::messageList(1002);
                $redata = $msg->toApiArray();
            }
        }
        self::getResponse()->setHeader(DefaultController::$defaultApiHeaders)->setResType('json')->setData(TA($redata))->send();
        return false;
    }

    /**
     * @return bool
     */
    protected function getAuthorize()
    {
        $rtype = self::getRequest()->getInput('response_type');
        $cid = self::getRequest()->getInput('client_id');
        $rurl = self::getRequest()->getInput('redirect_uri');
        $state = self::getRequest()->getInput('state');
        $scope = self::getRequest()->getInput('scope');
        if (!$rtype || $rtype != 'code') {
            $msg = Message::messageList(1004);
            $redata = $msg->toArray();
        } elseif (!$cid) {
            $msg = Message::messageList(1008);
            $redata = $msg->toArray();
        } elseif (!$rurl) {
            $msg = Message::messageList(1010);
            $redata = $msg->toArray();
        } else {
            //verify app
            $appu = new AppUtils(getDataPool()->getConnection('auth'));
            $app = $appu->verifyAppIdUri($cid, $rurl);
            if ($app) {
                $redata = ['app'=>$app, 'redirect_uri'=>$rurl];
                if ($state) {
                    $redata['state'] = $state;
                }
                if ($scope) {
                    $redata['scope'] = $scope;
                }
            } else {
                $msg = Message::messageList(1005);
                $redata = $msg->toArray();
            }
        }
        return self::getResponse()
            ->setHeader(DefaultController::$defaultViewHeaders)
            ->setResType('view')
            ->setView('oauth/login')
            ->setData(TA($redata))
            ->send();
    }

    /**
     * @return bool
     */
    protected function postAuthorize()
    {
        $username = self::getRequest()->getInput('username');
        $pwd = self::getRequest()->getInput('password');
        $scope = self::getRequest()->getInput('scope');
        $cid = self::getRequest()->getInput('client_id');
        $state = self::getRequest()->getInput('state');
        $redirect_uri = self::getRequest()->getInput('redirect_uri');
        if (!$cid) {
            $msg = Message::messageList(1008);
        } elseif (!$redirect_uri) {
            $msg = Message::messageList(1010);
        } elseif (!$username || !$pwd) {
            $msg = Message::messageList(21);
        } else {
            $u = [IAuth::KEY_USERNAME=>$username, IAuth::KEY_PASSWORD=>$pwd];
            $oauth = new OAuthUtils(getConfig()->get('auth'));
            $user = getAuth()->login($u);
            if ($user) {
                //generate code
                $code = $oauth->generateCode($user->getUserArray(), $cid, $redirect_uri);
                if ($code) {
                    $q = ['code' => $code];
                    if ($state) {
                        $q['state'] = $state;
                    }
                    $uri = $this->createUri($redirect_uri, $q);
                    \Flight::redirect($uri);
                    return false;
                } else {
                    $msg = Message::messageList(1001);
                }
            } else {
                $msg = Message::messageList(21);
            }
        }
        getOValue()->addOldOnce('msg', $msg->toArray());
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
        $uri = $this->createUri($uri, $q);
        \Flight::redirect($uri);
        return false;
    }

    /**
     * @param $uri
     * @param array $query
     * @return string
     */
    protected function createUri($uri, $query = [])
    {
        $p = [];
        foreach ($query as $k => $v) {
            array_push($p, "$k=$v");
        }
        return $uri . '?' . implode('&', $p);
    }
}