<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/23
 * Time: 16:34
 */

namespace Flight2wwu\Component\Database;


use DataPool\Common\DefaultDataPool;
use DataPool\Common\IDataPool;
use Flight2wwu\Common\ServiceProvider;

class DataPool implements ServiceProvider
{
    /**
     * @var IDataPool
     */
    private $dataPool;

    /**
     * Called after register.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Called after all class is registered.
     *
     * @return void
     */
    public function boot()
    {
        $conf = \Flight::get('datapool');
        $conf['debug'] = isDebug();
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