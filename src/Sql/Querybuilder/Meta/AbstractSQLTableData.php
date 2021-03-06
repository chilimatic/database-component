<?php
namespace chilimatic\lib\Database\Sql\Querybuilder\Meta;

use chilimatic\lib\Database\AbstractDatabase;

/**
 * Class AbstractSQLTableData
 * @package chilimatic\lib\Database\Sql\Querybuilder\meta
 */
abstract class AbstractSQLTableData
{
    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var array
     */
    protected $columnNames;

    /**
     * @var array
     */
    protected $columnNamesWithPrefix;

    /**
     * @var array
     */
    protected $columnData;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var AbstractDatabase
     */
    protected $db;

    /**
     * @var array
     */
    protected $primaryKey = [];

    /**
     * @param AbstractDatabase $db
     */
    public function __construct(AbstractDatabase $db) {
        $this->db = $db;
    }

    /**
     * @return void
     */
    abstract protected function fetchTableMetaData();

    /**
     * @return array
     */
    abstract public function getPrimaryKey();

    /**
     * @param string $columName
     *
     * @return mixed
     */
    abstract public function getColumnNameWithPrefix($columName);

    /**
     * @return mixed
     */
    abstract public function getColumnNamesWithPrefix();

    /**
     * @return mixed
     */
    abstract protected function fetchLazy();

    /**
     * @return array
     */
    abstract public function getColumnNames();

    /**
     * @param string $columnNames
     *
     * @return void
     */
    abstract public function setColumnsNames($columnNames);

    /**
     * @return array
     */
    abstract public function getColumnData();

    /**
     * @param array $columnData
     *
     * @return void
     */
    abstract public function setColumnData($columnData);

    /**
     * @param string $tableName
     *
     * @return string
     */
    private function generatePrefix($tableName) {
        return substr(md5($tableName), 0, 4);
    }

    /**
     * @return AbstractDatabase
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @param AbstractDatabase $db
     *
     * @return $this
     */
    public function setDb(AbstractDatabase $db)
    {
        $this->db = $db;

        return $this;
    }

    public function getTableNameWithPrefix() {
        return "$this->tableName `$this->prefix`";
    }

    /**
     * @return mixed
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param mixed $tableName
     *
     * @return $this
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
        $this->setPrefix($this->generatePrefix($tableName));
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param mixed $prefix
     *
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }
}