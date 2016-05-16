<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/29
 * Time: 16:14
 */

namespace App\Controller;


use App\Model\Auth\User;
use App\Model\Message;
use Flight2wwu\Common\BaseController;
use Flight2wwu\Common\FWException;
use League\Flysystem\Exception;

class OAuthController extends BaseController
{

    /**
     * Path to redirect after login.
     *
     * @var string
     */
    public static $redirectPath = '/';

    public static function login()
    {
        $scope = ['get_user_info']; //request scope
        $params = ['scope'=>implode(',', $scope)];
        $check_state = false;
        $uri = User::getAuthorizeUri($check_state, $params);
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
        if ($code) {
            $token = User::getAccessToken($code, $state);
            if (isset($token['access_token']) && isset($token['expires_in'])) {
                if (getAuth()->attempt(['access_token'=>$token['access_token'], 'expires_in'=>$token['expires_in']], false)) {
                    $path = getOValue()->getOldOnce('last_path');
                    if ($path) {
                        self::$redirectPath = $path;
                    }
                    \Flight::redirect(self::$redirectPath);
                    return false;
                } else {
                    throw new FWException(Message::getMessage(5));
                }
            } else {
                throw new FWException(Message::messageList(1003));
            }
        }
        throw new FWException(Message::messageList(1002));
    }
}