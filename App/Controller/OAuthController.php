<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/29
 * Time: 16:14
 */

namespace Wwtg99\App\Controller;



use GuzzleHttp\Client;
use Wwtg99\App\Model\Message;
use Wwtg99\Flight2wwu\Common\BaseController;
use Wwtg99\Flight2wwu\Common\FWException;

class OAuthController extends BaseController
{

    public static function login()
    {
//        $scope = ['get_user_info']; //TODO request scope
        $state = self::generateCSRFState();
        $params = ['state'=>$state, 'response_type'=>'code']; //TODO
        $uri = self::getAuthorizeUri($params);
        if ($uri) {
            \Flight::redirect($uri);
        } else {
            throw new FWException(Message::messageList(1001));
        }
        return false;
    }

    public static function redirect_login()
    {
        $code = self::getInput('code');
        $state = self::getInput('state');
        if (!self::verifyCSRFState($state)) {
            throw new FWException(Message::messageList(25));
        }
        if ($code) {
            $state = self::generateCSRFState();
            $params = ['state'=>$state, 'grant_type'=>'authorization_code']; //TODO
            $token = self::getAccessToken($code, $params);
            if (isset($token['access_token']) && isset($token['expires_in'])) {
                if (getAuth()->attempt($token)) {
                    $redirectPath = '/';
                    $path = getOValue()->getOldOnce('last_path');
                    if ($path) {
                        $redirectPath = $path;
                    }
                    \Flight::redirect($redirectPath);
                    return false;
                } else {
                    throw new FWException(Message::getMessage(15));
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
    private static function getAuthorizeUri($params = [])
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
    private static function getAccessToken($code, $params = [])
    {
        $oauth = getConfig()->get('oauth');
        $server_uri = $oauth['token_uri'];
        $appid = isset($oauth['app_id']) ? $oauth['app_id'] : '';
        $appid_key = isset($oauth['app_id_key']) ? $oauth['app_id_key'] : '';
        $appsec = isset($oauth['app_secret']) ? $oauth['app_secret'] : '';
        $appsec_key = isset($oauth['app_secret_key']) ? $oauth['app_secret_key'] : '';
        $redirect_uri = isset($oauth['redirect_uri']) ? $oauth['redirect_uri'] : '';
        $redirect_uri_key = isset($oauth['redirect_uri_key']) ? $oauth['redirect_uri_key'] : '';
        if ($server_uri && $redirect_uri && $redirect_uri_key && $code) {
            $params[$redirect_uri_key] = $redirect_uri;
            $params['code'] = $code;
            //app id and secret
            if ($appid && $appid_key && $appsec && $appsec_key) {
                $params[$appid_key] = $appid;
                $params[$appsec_key] = $appsec;
            }
            $client = new Client();
            $res = $client->get($server_uri, ['query'=>$params]);
            if ($res) {
                $json = json_decode($res->getBody(), true);
                //check state
                if (isset($params['state'])) {
                    $get_state = $json['state'];
                    if (!self::verifyCSRFState($get_state)) {
                        throw new FWException(Message::messageList(25));
                    }
                }
                return $json;
            }
        }
        return [];
    }
}