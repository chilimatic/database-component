<?php
namespace chilimatic\lib\Database;

use chilimatic\lib\Database\Connection\IDatabaseConnection;

/**
 * Class AbstractDatabase
 *
 * @package chilimatic\lib\Database
 */
abstract class AbstractDatabase implements DatabaseInterface
{
    /**
     * @param IDatabaseConnection $masterConnection
     * @param IDatabaseConnection $slaveConnection
     */
    abstract public function __construct(IDatabaseConnection $masterConnection, IDatabaseConnection $slaveConnection = null);

    /**
     * @param string $query
     *
     * @return mixed
     */
    abstract public function query($query);

    /**
     * @return mixed
     */
    abstract public function lastQuery();

    /**
     * @param string $query
     *
     * @return mixed
     */
    abstract public function execute($query);

    /**
     * @return mixed
     */
    abstract public function prepare($query);
}