<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2017/2/5
 * Time: 14:30
 */

namespace Wwtg99\Flight2wwu\Component\Controller\Internal;


use Wwtg99\App\Model\Message;
use Wwtg99\Flight2wwu\Common\FWException;
use Wwtg99\Flight2wwu\Component\Controller\BaseController;
use Wwtg99\Flight2wwu\Component\Utils\AjaxRequest;
use Wwtg99\PgAuth\Auth\IAuth;

class DefaultOAuthController extends BaseController
{

    public function login()
    {
        session_start();
        if (getAuth()->isLogin()) {
            $uri = U(getConfig()->get('defined_routes.user_center'));
            \Flight::redirect($uri);
        } else {
//            $scope = ['get_user_info']; //TODO request scope
            $state = getCSRF()->generateCSRFCode();
            $params = ['state'=>$state, 'response_type'=>'code']; //TODO use state and response_type
            $uri = $this->getAuthorizeUri($params);
            if ($uri) {
                \Flight::redirect($uri);
            } else {
                throw new FWException(Message::messageList(1001));
            }
        }
        return false;
    }

    public function redirect_login()
    {
        session_start();
        $code = self::getRequest()->getInput('code');
        $state = self::getRequest()->getInput('state');
        if (!getCSRF()->verifyCSRFCode($state)) {
            throw new FWException(Message::messageList(25));
        }
        if ($code) {
            $state = getCSRF()->generateCSRFCode();
            $params = ['state'=>$state, 'grant_type'=>'authorization_code']; //TODO use state and grant_type
            $token = $this->getAccessToken($code, $params);
            if (isset($token['access_token'])) {
                $u = [IAuth::KEY_TOKEN=>$token['access_token']];
                $user = getAuth()->login($u);
                if ($user) {
                    $redirectPath = '/';
                    $path = getOValue()->getOldOnce('last_path');
                    if ($path) {
                        $redirectPath = $path;
                    }
                    \Flight::redirect($redirectPath);
                    return false;
                } else {
                    throw new FWException(Message::getMessage(1001));
                }
            } else {
                throw new FWException(Message::messageList(1003));
            }
        }
        throw new FWException(Message::messageList(1002));
    }

    /**
     * @param array $params
     * @return string
     */
    protected function getAuthorizeUri($params = [])
    {
        $oauth = getConfig()->get('oauth');
        $appid = isset($oauth['app_id']) ? $oauth['app_id'] : '';
        $appid_key = isset($oauth['app_id_key']) ? $oauth['app_id_key'] : '';
        $redirect_uri = isset($oauth['redirect_uri']) ? $oauth['redirect_uri'] : '';
        $redirect_uri_key = isset($oauth['redirect_uri_key']) ? $oauth['redirect_uri_key'] : '';
        $server_uri = isset($oauth['code_uri']) ? $oauth['code_uri'] : '';
        if ($server_uri && $redirect_uri && $redirect_uri_key) {
            $params[$redirect_uri_key] = $redirect_uri;
            if ($appid && $appid_key) {
                $params[$appid_key] = $appid;
            }
            $p = [];
            foreach ($params as $k => $v) {
                array_push($p, "$k=$v");
            }
            $uri = $server_uri . '?' . implode('&', $p);
            return $uri;
        }
        return '';
    }

    /**
     * @param $code
     * @param array $params
     * @return array
     * @throws FWException
     */
    protected function getAccessToken($code, $params = [])
    {
        $oauth = getConfig()->get('oauth');
        $server_uri = $oauth['token_uri'];
        $appsec = isset($oauth['app_secret']) ? $oauth['app_secret'] : '';
        $appsec_key = isset($oauth['app_secret_key']) ? $oauth['app_secret_key'] : '';
        $redirect_uri = isset($oauth['redirect_uri']) ? $oauth['redirect_uri'] : '';
        $redirect_uri_key = isset($oauth['redirect_uri_key']) ? $oauth['redirect_uri_key'] : '';
        if ($server_uri && $redirect_uri && $redirect_uri_key && $code) {
            $params[$redirect_uri_key] = $redirect_uri;
            $params['code'] = $code;
            $params['state'] = getCSRF()->generateCSRFCode();
            //app id and secret
            if ($appsec && $appsec_key) {
                $params[$appsec_key] = $appsec;
            }
            $client = new AjaxRequest(['http_errors'=>false, 'timeout'=>10], 'json');
            $res = $client->get($server_uri, $params);
            if ($res) {
                if (isset($res['error'])) {
                    throw new FWException(Message::messageList(1003));
                }
                //check state
                if (!getCSRF()->verifyCSRFCode($res['state'])) {
                    throw new FWException(Message::messageList(25));
                }
                return $res;
            }
        }
        return [];
    }
}