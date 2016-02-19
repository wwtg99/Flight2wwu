<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/19
 * Time: 10:54
 */

namespace Flight2wwu\Component\Database;


use Flight2wwu\Common\ServiceProvider;
use Flight2wwu\Component\Log\Monolog;
use League\Flysystem\Exception;

class MedooDB implements ServiceProvider
{

    /**
     * @var \medoo
     */
    private $database;

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
        $this->reconnect();
    }

    public function getDB()
    {
        return $this->database;
    }

    /**
     * Execute query return all rows.
     *
     * @param string $query
     * @param array $data
     * @return array|bool
     */
    public function query($query, array $data = array())
    {
        $stmt = $this->prepare($query);
        $stmt = $this->executePrepare($stmt, $data);
        if ($stmt) {
            return $stmt->fetchAll();
        }
        return false;
    }

    /**
     * Execute query return first row.
     *
     * @param string $query
     * @param array $data
     * @return bool|array
     */
    public function queryOne($query, array $data = array())
    {
        $stmt = $this->prepare($query);
        $stmt = $this->executePrepare($stmt, $data);
        if ($stmt) {
            return $stmt->fetch();
        }
        return false;
    }

    /**
     * Execute update, insert or delete.
     *
     * @param string $query
     * @param array $data
     * @return bool|int
     */
    public function exec($query, array $data = array())
    {
        $stmt = $this->prepare($query);
        $stmt = $this->executePrepare($stmt, $data);
        if ($stmt) {
            return $stmt->rowCount();
        }
        return false;
    }

    /**
     * Prepare query
     *
     * @param string $query
     * @return \PDOStatement
     */
    public function prepare($query)
    {
        $query = str_replace(';', ' ', $query);
        $statement = $this->database->pdo->prepare($query);
        $statement->setFetchMode(\PDO::FETCH_ASSOC);
        return $statement;
    }

    /**
     * Execute prepared query.
     *
     * @param \PDOStatement $statement
     * @param array $data
     * @return bool|\PDOStatement
     */
    public function executePrepare(\PDOStatement $statement, array $data = array())
    {
        $this->bindValue($statement, $data);
        try {
            $re = $statement->execute();
            if (!$re) {
                $this->logError($this->getLastError(), $this->database->last_query());
                return false;
            }
            return $statement;
        } catch (\Exception $e) {
            $this->logError($this->getLastError(), $this->database->last_query());
            return false;
        }
    }

    /**
     * @param array $conf
     */
    public function connect(array $conf)
    {
        $db_conf = [
            'database_type' => $conf['driver'],
            'database_name' => $conf['dbname'],
            'server' => $conf['host'],
            'username' => $conf['user'],
            'password' => $conf['password'],
            'port' => $conf['port'],
            'charset' => 'utf8',
        ];
        if (array_key_exists('option', $conf)) {
            $db_conf['option'] = $conf['option'];
        }
        if (array_key_exists('prefix', $conf)) {
            $db_conf['prefix'] = $conf['prefix'];
        }
        $this->database = new \medoo($db_conf);
    }

    /**
     * @param string $name
     * @throws Exception
     */
    public function reconnect($name = 'main')
    {
        $conf = \Flight::get('database');
        if (!array_key_exists($name, $conf)) {
            throw new Exception("database config $name is not exists");
        }
        $main = $conf[$name];
        $this->connect($main);
    }

    /**
     * @return array
     */
    public function getLastError()
    {
        return $this->database->error();
    }

    /**
     * @param \PDOStatement $statement
     * @param array $data  [key1=>[value1, type1], key1=>value2, ...]
     */
    private function bindValue(\PDOStatement $statement, array $data)
    {
        if ($statement) {
            foreach ($data as $k => $v) {
                if (is_array($v) && count($v) > 1) {
                    $statement->bindValue($k, $v[0], $v[1]);
                } else {
                    $statement->bindValue($k, $v);
                }
            }
        }
    }

    /**
     * @param array $error
     * @param string $sql
     */
    private function logError(array $error, $sql = '')
    {
        $logger = getLog();
        if ($logger instanceof Monolog) {
            $logger->setCurrentLogger('database');
        }
        $logger->error("Code (" . $error['code'] . ') ' . $error['message'] . " by $sql");
        if ($logger instanceof Monolog) {
            $logger->setCurrentLogger();
        }
    }
} 