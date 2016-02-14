<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/4
 * Time: 17:05
 */

namespace App\Schedule;


use Flight2wwu\Schedule\ISchedule;

class DatabaseBackup implements ISchedule
{
    private $conf = [];

    /**
     * Call this function each time
     *
     * @return mixed
     */
    public function run()
    {
        switch ($this->conf['driver']) {
            case 'pgsql': $this->backupPgsql(); break;
        }
    }

    /**
     * Register schedule
     *
     * @param array $conf
     * @return mixed
     */
    public function register($conf)
    {
        $db = $conf['database'];
        if (array_key_exists('backup', $db)) {
            $this->conf = $db['backup'];
        } else {
            $this->conf = $db['main'];
        }
    }

    private function backupPgsql()
    {
        $host = array_key_exists('host', $this->conf) ? ('-h ' . $this->conf['host']) : '';
        $dbname = array_key_exists('dbname', $this->conf) ? ('-d ' . $this->conf['dbname']) : '';
        $user = array_key_exists('user', $this->conf) ? ('-U ' . $this->conf['user']) : '';
        $port = array_key_exists('port', $this->conf) ? ('-p ' . $this->conf['port']) : '';
        $cmd = "pg_dump -h $host -p $port -d $dbname -U $user";
        $re = exec($cmd);
    }

}