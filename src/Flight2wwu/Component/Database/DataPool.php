<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/23
 * Time: 16:34
 */

namespace Flight2wwu\Component\Database;


use Wwtg99\DataPool\Common\DefaultDataPool;
use Wwtg99\DataPool\Common\IDataPool;

class DataPool
{
    /**
     * @var IDataPool
     */
    private $dataPool;

    /**
     * DataPool constructor.
     * @param array $conf
     */
    public function __construct($conf = [])
    {
        if (!$conf) {
            $conf = \Flight::get('config')->get('datapool');
            $conf['debug'] = isDebug();
        }
        $this->dataPool = new DefaultDataPool($conf, ROOT);
    }

    /**
     * @return IDataPool
     */
    public function getDataPool()
    {
        return $this->dataPool;
    }
}