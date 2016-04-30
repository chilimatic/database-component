<?php
namespace chilimatic\lib\Database\Sql\Mysql\Connection;

use chilimatic\lib\Database\Sql\Connection\AbstractSQLConnection;
use chilimatic\lib\Database\Sql\Connection\AbstractSQLConnectionSettings;
use chilimatic\lib\Database\Sql\Connection\ISQLConnectionStorage;

/**
 * Class MySQLConnectionStorage
 * @package chilimatic\lib\Database\Sql\Mysql\Connection
 */
class MySQLConnectionStorage implements ISQLConnectionStorage
{
    /**
     * @var \SplObjectStorage
     */
    protected $storage;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->storage = new \SplObjectStorage();
    }


    /**
     * @param AbstractSQLConnection $connection
     *
     * @return void
     */
    public function addConnection(AbstractSQLConnection $connection){
        $this->storage->attach($connection);
    }

    /**
     * @param AbstractSQLConnectionSettings $connectionSettings
     * @param string $adapterName
     *
     * @return void
     * @throws \chilimatic\lib\Database\Exception\DatabaseException
     */
    public function addConnectionBySetting(AbstractSQLConnectionSettings $connectionSettings, $adapterName = '') {
        $this->storage->attach(
            new MySQLConnection($connectionSettings, $adapterName)
        );
    }

    /**
     * @param $pos
     *
     * @return null|AbstractSQLConnection
     */
    public function getConnectionByPosition($pos)
    {
        $pos = (int) $pos;
        $this->storage->rewind();
        for ($i = 0; $this->storage->count() > $i; $i++) {
            if ($i === $pos) {
                return $this->storage->current();
            }
            $this->storage->next();
        }

        return null;
    }

    /**
     * @param AbstractSQLConnection $connection
     *
     * @return bool
     */
    public function findConnection(AbstractSQLConnection $connection)
    {
        if ($this->storage->contains($connection)) {
            return true;
        }

        return false;
    }


    /**
     * removes a connection of the pool
     *
     * @param AbstractSQLConnection $connection
     *
     * @return void
     */
    public function removeConnection(AbstractSQLConnection $connection)
    {
        $this->storage->detach($connection);
    }
}