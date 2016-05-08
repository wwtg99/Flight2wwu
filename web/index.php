<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/2
 * Time: 10:55
 */

require '../bootstrap/init.php';

if (Flight::get('maintain')) {
    header('HTTP/1.1 503 Service Unavailable');
    echo 'Maintenance';
} else {
    Flight::start();
}
