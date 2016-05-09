<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/4/15
 * Time: 18:17
 */

namespace App\Model;


class Message
{

    /**
     * @param int $code
     * @param string $message
     * @param string $type
     * @return array
     */
    public static function getMessage($code = 0, $message = '', $type = '')
    {
        switch ($code) {
            case 1: $err = ['message'=>'illegal id or name', 'code'=>$code, 'type'=>'danger']; break;
            case 2: $err = ['message'=>'fail to create', 'code'=>$code, 'type'=>'danger']; break;
            case 3: $err = ['message'=>'fail to update', 'code'=>$code, 'type'=>'danger']; break;
            case 4: $err = ['message'=>'fail to delete', 'code'=>$code, 'type'=>'danger']; break;
            case 5: $err = ['message'=>'login failed', $code=>$code, 'type'=>'danger']; break;
            case 6: $err = ['message'=>'password mismatch', $code=>$code, 'type'=>'danger']; break;
            case 7: $err = ['message'=>'password changed', $code=>$code, 'type'=>'success']; break;
            case 8: $err = ['message'=>'password not changed', $code=>$code, 'type'=>'danger']; break;
            case 9: $err = ['message'=>'empty input', $code=>$code, 'type'=>'danger']; break;
            default: $err = ['message'=>'', 'code'=>0, 'type'=>'info']; break;
        }
        if ($message) {
            $err['message'] = $message;
        }
        if ($type) {
            $err['type'] = $type;
        }
        return $err;
    }
}