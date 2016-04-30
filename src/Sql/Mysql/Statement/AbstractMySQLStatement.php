<?php

namespace chilimatic\lib\Database\Sql\Mysql\Statement;


use chilimatic\lib\Database\Connection\IDatabaseConnectionAdapter;
use chilimatic\lib\Database\Sql\Connection\AbstractSQLConnectionAdapter;

/**
 * Class AbstractMysqlStatement
 *
 * @package chilimatic\lib\Database\sql\mysql\statement
 */
abstract class AbstractMySQLStatement implements IMySQLStatement
{
    /**
     * @var AbstractSQLConnectionAdapter
     */
    private $dbAdapter;

    /**
     * @param IDatabaseConnectionAdapter $connectionAdapter
     */
    public function __construct(IDatabaseConnectionAdapter $connectionAdapter)
    {
        $this->dbAdapter = $connectionAdapter;
    }

    /**
     * @param string $name
     * @param mixed $var
     * @param array $options
     *
     * @return mixed
     */
     abstract public function bindParam($name, &$var, array $options = []);

    /**
     * @return mixed
     */
    abstract public function rewind();

    /**
     * @return mixed
     */
    public function getErrorInfo() {
        $this->getDbAdapter()->getErrorInfo();
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        $this->getDbAdapter()->getErrorCode();
    }

    /**
     * @return int
     */
    abstract public function rowCount();


    /**
     * @return int
     */
    abstract public function columnCount();


    /**
     * @return bool
     */
    abstract public function execute();

    /**
     * @param array $options
     *
     * @return void
     */
    abstract public function setOptions(array $options = []);

    /**
     * @param int $postion
     *
     * @return array
     */
    abstract public function fetchColumn($postion);

    /**
     * @return int
     */
    abstract public function getAffectedRows();

    /**
     * @return \stdClass
     */
    abstract public function fetchObject();

    /**
     * @param $resultType
     *
     * @return array
     */
    abstract public function fetchAll($resultType);

    /**
     * @return int
     */
    abstract public function getInsertId();

    /**
     * @return AbstractSQLConnectionAdapter
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    /**
     * @param AbstractSQLConnectionAdapter $dbAdapter
     *
     * @return $this
     */
    public function setDbAdapter($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;

        return $this;
    }

}