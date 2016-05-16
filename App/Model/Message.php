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
            case 1: $msg = ['message'=>'illegal id or name', 'type'=>'danger']; break;
            case 2: $msg = ['message'=>'fail to create', 'type'=>'danger']; break;
            case 3: $msg = ['message'=>'fail to update', 'type'=>'danger']; break;
            case 4: $msg = ['message'=>'fail to delete', 'type'=>'danger']; break;
            case 5: $msg = ['message'=>'login failed', 'type'=>'danger']; break;
            case 6: $msg = ['message'=>'password mismatch', 'type'=>'danger']; break;
            case 7: $msg = ['message'=>'password changed', 'type'=>'success']; break;
            case 8: $msg = ['message'=>'password not changed', 'type'=>'danger']; break;
            case 9: $msg = ['message'=>'empty input', 'type'=>'danger']; break;
            case 1001: $msg = ['message'=>'illegal oauth', 'type'=>'danger']; break;
            case 1002: $msg = ['message'=>'no code', 'type'=>'danger']; break;
            case 1003: $msg = ['message'=>'fail to get access_token', 'type'=>'danger']; break;
            default: $msg = ['message'=>'', 'type'=>'info']; $code = 0; break;
        }
        return new Message($code, $msg['message'], $msg['type']);
    }

}