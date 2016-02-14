<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/4
 * Time: 16:24
 */

require "../bootstrap/init.php";
$app_conf = CONFIG . 'app_config.php';
$manager = new \Flight2wwu\Schedule\ScheduleManager($app_conf);
$manager->register();
$manager->run();
$manager->enableService();