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
    public static function getMessage($code = 0, $message = '', $type = 'danger')
    {
        switch ($code) {
            case 1: $err = ['message'=>'illegal id or name', 'code'=>$code]; break;
            case 2: $err = ['message'=>'fail to create', 'code'=>$code]; break;
            case 3: $err = ['message'=>'fail to update', 'code'=>$code]; break;
            case 4: $err = ['message'=>'fail to delete', 'code'=>$code]; break;
            default: $err = ['message'=>'', 'code'=>0]; break;
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