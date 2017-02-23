<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/19
 * Time: 10:54
 */

namespace Wwtg99\Flight2wwu\Component\Database;
use Medoo\Medoo;


/**
 * Class MedooDB
 * @package Flight2wwu\Component\Database
 */
class MedooDB extends Medoo
{

    /**
     * MedooDB constructor.
     * @param array $conf
     */
    public function __construct($conf = [])
    {
        if (!$conf) {
            $conf = \Flight::get('config')->get('database');
        }
        if (!isset($conf['charset'])) {
            $conf['charset'] = 'utf8';
        }
        parent::__construct($conf);
    }
} 