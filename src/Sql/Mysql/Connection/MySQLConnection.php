<?php
/**
 *
 * @author j
 * Date: 12/22/14
 * Time: 5:26 PM
 *
 * File: mysqlconnection.php
 */

namespace chilimatic\lib\Database\Sql\Mysql\Connection;

use chilimatic\lib\Database\Connection\IDatabaseConnectionSettings;
use chilimatic\lib\Database\Mock\Adapter\MockMysqlConnectionAdapter;
use chilimatic\lib\Database\Sql\Connection\AbstractSQLConnection;
use chilimatic\lib\Database\Sql\Mysql\Connection\Adapter\MySQLiConnectionAdapter;
use chilimatic\lib\Database\Sql\Mysql\Connection\Adapter\PDOConnectionAdapter;
use chilimatic\lib\Database\Exception\DatabaseException;


/**
 * <h1>Class MysqlConnection</h1>
 * <p>
 * all connection parameters are injected from the outside. So only the validation is from within
 * </p>
 *
 * Class MysqlConnection
 *
 * @package chilimatic\lib\Database\sql\mysql\connection
 */
class MySQLConnection extends AbstractSQLConnection
{
    /**
     * the currently available connection Interfaces
     * and the mocking interface
     *
     * @var string
     */
    const CONNECTION_PDO        = 'pdo';
    const CONNECTION_MYSQLI     = 'mysqli';
    const CONNECTION_MOCK       = 'mock';


    /**
     * @param IDatabaseConnectionSettings $connectionSettings
     * @param string $adapterName
     *
     * @throws DatabaseException
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function prepareAndInitializeAdapter(IDatabaseConnectionSettings $connectionSettings, $adapterName)
    {
        if (!$adapterName) {
            throw new \InvalidArgumentException('The AdapterName was not specified, this field is not Optional in the MySQL Connection');
        }

        switch ($adapterName) {
            case self::CONNECTION_PDO:
                $this
                    ->setDbAdapter(new PDOConnectionAdapter($connectionSettings))
                    ->checkForConnectionStatus();
            break;
            case self::CONNECTION_MYSQLI:
                $this
                    ->setDbAdapter(new MySQLiConnectionAdapter($connectionSettings))
                    ->checkForConnectionStatus();

                break;
            case self::CONNECTION_MOCK:
                $this
                    ->setDbAdapter(new MockMysqlConnectionAdapter($connectionSettings))
                    ->checkForConnectionStatus();

                break;
            default:
                throw new \InvalidArgumentException('The AdapterName was wrong only pdo and mysqli are allowed');
        }
    }

    /**
     * checks if the adapter ha a valid connection
     */
    private function checkForConnectionStatus() {
        if ($this->ping()) {
            $this->setConnected(true);
            return;
        }
        $this->setConnected(false);
    }


    /**
     * Ping
     * <p>
     * reconnect to mysql if the connection was lost
     * </p>
     *
     * @return bool
     */
    public function ping()
    {
        return $this->getDbAdapter()->ping();
    }


    /**
     * since this method is a wrapper for the tryReconnect only limited
     * we set a max amount of retries.
     * increase the counter and reconnect
     * as often as defined
     *
     * @return bool
     */
    public function reconnect()
    {
        if ($this->getMaxReconnects() < $this->getReconnectCount()) {
            return false;
        }

        $this->increaseReconnectCount();
        // returns the state if the ping was possible
        return $this->ping();
    }
}