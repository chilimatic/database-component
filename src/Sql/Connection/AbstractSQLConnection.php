<?php
namespace chilimatic\lib\Database\Sql\Connection;

use chilimatic\lib\Database\Connection\IDatabaseConnection;
use chilimatic\lib\Database\Connection\IDatabaseConnectionAdapter;
use chilimatic\lib\Database\Connection\IDatabaseConnectionSettings;
use chilimatic\lib\Database\Sql\Mysql\Connection\MySQLConnectionSettings;
use chilimatic\lib\Database\Exception\DatabaseException;


/**
 * Class AbstractSqlConnection
 *
 * @package chilimatic\lib\Database\sql
 */
abstract class AbstractSQLConnection implements IDatabaseConnection, ISQLConnection {

    /**
     * if it's active (in use)
     *
     * @var bool
     */
    protected $active = false;

    /**
     * the socket
     *
     * @var bool
     */
    private $socket;

    /**
     * @var int
     */
    private $lastPing;

    /**
     * amount of current reconnects
     *
     * @var int
     */
    private $reconnectCount;

    /**
     * connection Role
     * @var int
     */
    private $connectionRole;

    /**
     * amount of max reconnects
     *
     * @var int
     */
    private $maxReconnects = self::MAX_DEFAULT_RECONNECTS;

    /**
     * current status of connection
     *
     * @var bool
     */
    private $connected = false;

    /**
     * the connection
     *
     * @var IDatabaseConnectionAdapter
     */
    private $dbAdapter;


    /**
     * AbstractSQLConnection constructor.
     * @param IDatabaseConnectionSettings $connectionSettings
     * @param string $adapterName
     * @throws \chilimatic\lib\Database\Exception\DatabaseException
     */
    public function __construct(IDatabaseConnectionSettings $connectionSettings, $adapterName = '') 
    {
        // initializes the needed steps for the Connection
        $this->prepareAndInitializeAdapter($connectionSettings, $adapterName);
    }

    /**
     * @param IDatabaseConnectionSettings $connectionSettings
     * @param $adapterName
     *
     * @throws DatabaseException
     *
     * @return mixed
     */
    abstract public function prepareAndInitializeAdapter(IDatabaseConnectionSettings $connectionSettings, $adapterName);

    /**
     * a database connection needs certain parameters to work
     *
     * Host | Username | Password these are the minimum requirements which have to be checked
     * the secondary parameters like database and port need to be checked if they're set as well
     *
     * @return bool
     */
    public function connectionSettingsAreValid()
    {
        // if there is no adapter how can we initialize the correct validator to check it
        if (!$this->getDbAdapter()) {
            return false;
        }

        /**
         * @var MySQLConnectionSettings $connectionSettings
         */
        $connectionSettings = $this->getDbAdapter()->getConnectionSettings();
        if (!$connectionSettings) {
            return false;
        }

        return $connectionSettings->validateProperties();
    }


    /**
     * @return mixed
     */
    abstract public function ping();


    /**
     * @return mixed
     */
    abstract public function reconnect();


    /**
     * @return boolean
     */
    public function isActive()
    {
        return (bool) $this->active;
    }

    /**
     * @param boolean $active
     *
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSocket()
    {
        return $this->socket;
    }

    /**
     * @param boolean $socket
     *
     * @return $this
     */
    public function setSocket($socket)
    {
        $this->socket = (bool) $socket;

        return $this;
    }

    /**
     * increments the reconnect counter
     */
    public function increaseReconnectCount()
    {
        $this->reconnectCount++;
    }

    /**
     * @return int
     */
    public function getLastPing()
    {
        return $this->lastPing;
    }

    /**
     * @param int $lastPing
     *
     * @return $this
     */
    public function setLastPing($lastPing)
    {
        $this->lastPing = $lastPing;

        return $this;
    }

    /**
     * @return int
     */
    public function getReconnectCount()
    {
        return $this->reconnectCount;
    }

    /**
     * @param int $reconnectCount
     *
     * @return $this
     */
    public function setReconnectCount($reconnectCount)
    {
        $this->reconnectCount = (int) $reconnectCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxReconnects()
    {
        return $this->maxReconnects;
    }

    /**
     * @param int $maxReconnects
     *
     * @return $this
     */
    public function setMaxReconnects($maxReconnects)
    {
        $this->maxReconnects = (int) $maxReconnects;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isConnected()
    {
        return $this->connected;
    }

    /**
     * @param boolean $connected
     *
     * @return $this
     */
    public function setConnected($connected)
    {
        $this->connected = $connected;

        return $this;
    }

    /**
     * @return AbstractSQLConnectionAdapter
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    /**
     * @param IDatabaseConnectionAdapter $dbAdapter
     *
     * @return $this
     */
    public function setDbAdapter(IDatabaseConnectionAdapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;

        return $this;
    }

    /**
     * @return int
     */
    public function getConnectionRole()    {
        return $this->connectionRole;
    }

    /**
     * @param int $connectionRole
     *
     * @return $this
     */
    public function setConnectionRole($connectionRole)
    {
        $this->connectionRole = $connectionRole;

        return $this;
    }
}