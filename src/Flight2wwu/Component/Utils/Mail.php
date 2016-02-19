<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/19
 * Time: 14:33
 */

namespace Flight2wwu\Component\Utils;


/**
 * Class Mail
 * Send mail depends on SwiftMail
 * @package Flight2wwu\Component\Utils
 */
class Mail {

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @param \Swift_Mailer $mailer
     */
    function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param array|\Swift_Message $message
     * @return int|null
     */
    public function send($message)
    {
        if (is_array($message)) {
            $subject = $message['subject'];
            $from = $message['from'];
            $to = $message['to'];
            $body = $message['body'];
            $ctype = array_key_exists('content_type', $message) ? $message['content_type'] : null;
            $msg = \Swift_Message::newInstance($subject, $body, $ctype, 'UTF-8')->setFrom($from)->setTo($to);
            if (array_key_exists('id', $message)) {
                $msg->setId($message['id']);
            }
            if (array_key_exists('cc', $message)) {
                $msg->setCc($message['cc']);
            }
            if (array_key_exists('bcc', $message)) {
                $msg->setBcc($message['bcc']);
            }
            if (array_key_exists('reply-to', $message)) {
                $msg->setReplyTo($message['reply-to']);
            }
            if (array_key_exists('date', $message)) {
                $msg->setDate($message['date']);
            }
            return $this->mailer->send($msg);
        } elseif ($message instanceof \Swift_Message) {
            return $this->mailer->send($message);
        } else {
            return null;
        }
    }

    /**
     * @param string $type
     * @param array $param
     * @return Mail
     */
    public static function getMail($type = 'mail', $param = [])
    {
        switch($type) {
            case 'smtp':
                $host = array_key_exists('host', $param) ? $param['host'] : 'localhost';
                $port = array_key_exists('port', $param) ? $param['port'] : 25;
                $security = array_key_exists('security', $param) ? $param['security'] : null;
                $tran = \Swift_SmtpTransport::newInstance($host, $port, $security)->setUsername($param['username'])->setPassword($param['password']);
                break;
            case 'sendmail':
                $cmd = array_key_exists('command', $param) ? $param['command'] : '/usr/sbin/sendmail -bs';
                $tran = \Swift_SendmailTransport::newInstance($cmd);
                break;
            case 'mail':
            default:
                $tran = \Swift_MailTransport::newInstance();
                break;
        }
        return new Mail(\Swift_Mailer::newInstance($tran));
    }

} 