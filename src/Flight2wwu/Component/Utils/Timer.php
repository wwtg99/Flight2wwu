<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/11/23
 * Time: 11:30
 */

namespace Wwtg99\Flight2wwu\Component\Utils;


class Timer
{

    /**
     * @var string
     */
    protected $timezone;

    /**
     * @var string
     */
    protected $format = 'Y-m-d H:i:s';

    /**
     * Timer constructor.
     * @param array $conf
     */
    public function __construct($conf = [])
    {
        if (!$conf) {
            $tz = \Flight::get('config')->get('timezone');
            $conf = ['timezone'=>$tz];
        }
        $this->loadConfig($conf);
    }

    /**
     * @param array $conf
     */
    public function loadConfig(array $conf)
    {
        $this->timezone = isset($conf['timezone']) ? $conf['timezone'] : '';
        if ($this->timezone) {
            date_default_timezone_set($this->timezone);
        }
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @return bool|string
     */
    public function getNow()
    {
        return date($this->format);
    }

    /**
     * @param $time
     * @return string
     */
    public function formatTime($time)
    {
        return date_create($time)->format($this->format);
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

}