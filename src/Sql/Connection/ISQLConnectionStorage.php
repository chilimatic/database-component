<?php
namespace chilimatic\lib\Database\Sql\Connection;

/**
 * Interface ISQLConnectionStorage
 * @package chilimatic\lib\Database\Sql\Connection
 */
interface ISQLConnectionStorage
{

    /**
     * ISQLConnectionStorage constructor.
     */
    public function __construct();

    /**
     * @param AbstractSQLConnection $connection
     *
     * @return mixed
     */
    public function addConnection(AbstractSQLConnection $connection);

    /**
     * @param AbstractSQLConnectionSettings $connectionSettings
     * @param string $adapterName
     *
     * @return mixed
     */
    public function addConnectionBySetting(AbstractSQLConnectionSettings $connectionSettings, $adapterName = '');

    /**
     * @param AbstractSQLConnection $connection
     *
     * @return bool
     */
    public function findConnection(AbstractSQLConnection $connection);


    /**
     * @param AbstractSQLConnection $connection
     *
     * @return true|null
     */
    public function removeConnection(AbstractSQLConnection $connection);
}