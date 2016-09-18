<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/9/18
 * Time: 17:06
 */

namespace Wwtg99\Flight2wwu\Component\Database;


use Predis\Client;

class PRedis extends Client
{

    /**
     * PRedis constructor.
     *
     * @param array $conf
     * @param $options
     */
    public function __construct($conf = [], $options = null)
    {
        if (!$conf) {
            $conf = \Flight::get('config')->get('redis');
        }
        parent::__construct($conf, $options);
    }
}