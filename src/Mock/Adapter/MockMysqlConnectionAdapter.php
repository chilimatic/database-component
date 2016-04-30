<?php
namespace chilimatic\lib\Database\Mock\Adapter;

use chilimatic\lib\Database\Sql\Connection\AbstractSQLConnectionAdapter;

/**
 * Class MockMysqlConnectionAdapter
 * @package chilimatic\lib\Database\Mock\Adapter
 */
class MockMysqlConnectionAdapter extends AbstractSQLConnectionAdapter
{
    /**
     * @var bool
     */
    private $inTransaction = false;


    /**
     * @return bool
     */
    public function initResource()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function ping()
    {
        return true;
    }

    /**
     * @param string $sql
     * @param array $options
     * @return void
     */
    public function query($sql,array $options = [])
    {
        // TODO: Implement query() method.
    }

    /**
     * @param $sql
     * @param array $options
     *
     * @return bool
     */
    public function prepare($sql,array  $options = [])
    {
        return true;
    }

    /**
     * @param string $sql
     *
     * @return bool
     */
    public function execute($sql)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function beginTransaction()
    {
        $this->inTransaction = true;
        return true;
    }

    /**
     * @return bool
     */
    public function inTransaction()
    {
        return $this->inTransaction;
    }

    /**
     * @return bool
     */
    public function rollback()
    {
        $this->inTransaction = false;
        return true;
    }

    /**
     * @return bool
     */
    public function commit()
    {
        $this->inTransaction = false;
        return true;
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        // return an integer value
        return 12345;
    }

    /**
     * @return array
     */
    public function getErrorInfo()
    {
        return [];
    }

    /**
     * @return mixed
     */
    public function getLastInsertId()
    {
        return 12;
    }

}