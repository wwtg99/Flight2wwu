<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/8
 * Time: 11:59
 */

namespace Wwtg99\Flight2wwu\Component\Log;


use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Symfony\Component\Config\Definition\Exception\Exception;
use Wwtg99\Flight2wwu\Component\Utils\FormatUtils;

class Monolog implements ILog
{

    /**
     * @var array
     */
    private $loggers = [];

    /**
     * @var string
     */
    private $current = 'main';

    /**
     * @var string
     */
    private $logDir = '';

    /**
     * Monolog constructor.
     * @param array $conf
     */
    function __construct($conf = [])
    {
        if (!$conf) {
            $conf = \Flight::get('config')->get('log');
        }
        if (is_array($conf)) {
            $this->logDir = isset($conf['directory']) ? FormatUtils::formatPath($conf['directory']) : STORAGE . 'log';
            if (!file_exists($this->logDir)) {
                mkdir($this->logDir, 0777, true);
            }
            if (isset($conf['loggers'])) {
                foreach ($conf['loggers'] as $d => $con) {
                    $this->registerLogger($d, $con);
                }
            }
        }
    }

    /**
     * @param string $domain
     * @param array $config
     * @return Logger
     */
    public function registerLogger($domain, array $config)
    {
        $logger = new Logger($domain);
        $level = isset($config['level']) ? self::getLevel($config['level']) : '';
        $name = isset($config['title']) ? $config['title'] : "$domain.log";
        $max = isset($config['max_logfile']) ? intval($config['max_logfile']) : 10;
        $handler = new RotatingFileHandler(FormatUtils::formatPathArray([$this->logDir, $name]), $max, $level, true, 0777);
        $logger->pushHandler($handler);
        $this->loggers[$domain] = $logger;
        return $this->loggers[$domain];
    }

    /**
     * @param string $domain
     * @return Logger|array
     */
    public function getLogger($domain = 'main')
    {
        if (is_null($domain)) {
            return $this->loggers;
        }
        if (!array_key_exists($domain, $this->loggers)) {
            $domain = 'main';
        }
        return $this->loggers[$domain];
    }

    /**
     * @param $name
     * @return $this
     */
    public function changeLogger($name = 'main')
    {
        $this->current = $name;
        if (!array_key_exists($this->current, $this->loggers)) {
            $this->current = 'main';
        }
        return $this;
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function debug($msg, array $context = array())
    {
        return $this->getLogger($this->current)->addDebug($msg, $context);
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function info($msg, array $context = array())
    {
        return $this->getLogger($this->current)->addInfo($msg, $context);
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function notice($msg, array $context = array())
    {
        return $this->getLogger($this->current)->addNotice($msg, $context);
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function warning($msg, array $context = array())
    {
        return $this->getLogger($this->current)->addWarning($msg, $context);
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function error($msg, array $context = array())
    {
        return $this->getLogger($this->current)->addError($msg, $context);
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function critical($msg, array $context = array())
    {
        return $this->getLogger($this->current)->addCritical($msg, $context);
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function alert($msg, array $context = array())
    {
        return $this->getLogger($this->current)->addAlert($msg, $context);
    }

    /**
     * @param string $msg
     * @param array $context
     * @return bool
     */
    public function emergency($msg, array $context = array())
    {
        return $this->getLogger($this->current)->addEmergency($msg, $context);
    }

    /**
     * @param string $level
     * @return int
     */
    public static function getLevel($level) {
        $l = strtoupper($level);
        switch ($l) {
            case 'DEBUG': return Logger::DEBUG;
            case 'INFO': return Logger::INFO;
            case 'NOTICE': return Logger::NOTICE;
            case 'WARNING': return Logger::WARNING;
            case 'ERROR': return Logger::ERROR;
            case 'CRITICAL': return Logger::CRITICAL;
            case 'ALERT': return Logger::ALERT;
            case 'EMERGENCY': return Logger::EMERGENCY;
            default: return Logger::ERROR;
        }
    }
} 