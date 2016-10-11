<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/4/15
 * Time: 18:17
 */

namespace Wwtg99\App\Model;

/**
 * Class Message
 * Error code list.
 * @package Wwtg99\App\Model
 */
class Message
{

    /**
     * @var int
     */
    private $code;

    /**
     * @var string
     */
    private $msg;

    /**
     * @var string
     */
    private $type;

    /**
     * Message constructor.
     * @param int $code
     * @param string $message
     * @param string $type
     */
    public function __construct($code, $message, $type = 'danger')
    {
        $this->code = $code;
        $this->msg = $message;
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * @param string $msg
     */
    public function setMsg($msg)
    {
        $this->msg = $msg;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return ['message'=>$this->msg, 'code'=>$this->code, 'type'=>$this->type];
    }

    /**
     * @param int $code
     * @param string $message
     * @param string $type
     * @return array
     */
    public static function getMessage($code = 0, $message = '', $type = '')
    {
        $msg = self::messageList($code);
        if ($message) {
            $msg->setMsg($message);
        }
        if ($type) {
            $msg->setType($type);
        }
        return $msg->toArray();
    }

    /**
     * @param int $code
     * @return Message
     */
    public static function messageList($code)
    {
        switch ($code) {
            // fatal error
            case 1: $msg = ['message'=>'server error', 'type'=>'danger']; break;
            // common error
            case 11: $msg = ['message'=>'invalid id or name', 'type'=>'danger']; break;
            case 12: $msg = ['message'=>'fail to create', 'type'=>'danger']; break;
            case 13: $msg = ['message'=>'fail to update', 'type'=>'danger']; break;
            case 14: $msg = ['message'=>'fail to delete', 'type'=>'danger']; break;
            case 15: $msg = ['message'=>'empty input', 'type'=>'danger']; break;
            // auth error
            case 21: $msg = ['message'=>'login failed', 'type'=>'danger']; break;
            case 22: $msg = ['message'=>'password mismatch', 'type'=>'danger']; break;
            case 23: $msg = ['message'=>'password changed', 'type'=>'success']; break;
            case 24: $msg = ['message'=>'password not changed', 'type'=>'danger']; break;
            case 25: $msg = ['message'=>'CSRF token invalid', 'type'=>'danger']; break;
            case 26: $msg = ['message'=>'sign up failed', 'type'=>'danger']; break;
            case 27: $msg = ['message'=>'invalid captcha', 'type'=>'danger']; break;
            case 28: $msg = ['message'=>'user info changed', 'type'=>'success']; break;
            case 29: $msg = ['message'=>'user info changed failed', 'type'=>'danger']; break;
            // oauth error
            case 1001: $msg = ['message'=>'oauth login failed', 'type'=>'danger']; break;
            case 1002: $msg = ['message'=>'invalid code', 'type'=>'danger']; break;
            case 1003: $msg = ['message'=>'fail to get access_token', 'type'=>'danger']; break;
            case 1004: $msg = ['message'=>'invalid response_type', 'type'=>'danger']; break;
            case 1005: $msg = ['message'=>'invalid client_id or redirect uri', 'type'=>'danger']; break;
            case 1006: $msg = ['message'=>'invalid grant_type', 'type'=>'danger']; break;
            case 1007: $msg = ['message'=>'invalid client_id and client_secret', 'type'=>'danger']; break;
            case 1008: $msg = ['message'=>'client_id does not exists', 'type'=>'danger']; break;
            case 1009: $msg = ['message'=>'invalid client_id', 'type'=>'danger']; break;
            case 1010: $msg = ['message'=>'redirect uri does not exists', 'type'=>'danger']; break;
            case 1011: $msg = ['message'=>'app secret does not exists', 'type'=>'danger']; break;
            case 1012: $msg = ['message'=>'invalid access_token', 'type'=>'danger']; break;
            default: $msg = ['message'=>'', 'type'=>'info']; $code = 0; break;
        }
        return new Message($code, $msg['message'], $msg['type']);
    }

}