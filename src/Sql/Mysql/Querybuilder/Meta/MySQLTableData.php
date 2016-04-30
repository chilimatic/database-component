<?php

namespace chilimatic\lib\Database\Sql\Mysql\Querybuilder\Meta;
use chilimatic\lib\Database\Sql\Querybuilder\Meta\AbstractSQLTableData;


/**
 * Class MySQLTableData
 *
 * @package chilimatic\lib\Database\Sql\Mysql\Querybuilder\Meta
 */
class MySQLTableData extends AbstractSQLTableData
{

    /**
     * @throws \Exception
     */
    protected function fetchTableMetaData()
    {
        if (!$this->db || !$this->tableName) {
            return;
        }

        /**
         * @var \PDOStatement $stmt
         */
        $stmt = $this->db->getDb()->query('desc ' . $this->tableName);
        if (!$stmt) {
            throw new \Exception('Could not get table description :' . print_r($this->db->getDb()->errorInfo(), true));
        }

        $this->columnData = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $set) {
            $this->columnData[$set['Field']] = $set;
        }
        unset($set, $stmt);
    }

    /**
     * @return array
     */
    public function getPrimaryKey()
    {
        if ($this->primaryKey) {
            return $this->primaryKey;
        }

        foreach ($this->getColumnData() as $data) {
            if (!empty($data['Key']) && $data['Key'] === 'PRI') {
                $this->primaryKey[] = $data['Field'];
            }
        }

        return $this->primaryKey;
    }

    /**
     * @param $columName
     *
     * @return null
     */
    public function getColumnNameWithPrefix($columName)
    {
        if (!isset($this->columnNamesWithPrefix[$columName])) {
            return null;
        }

        return $this->columnNamesWithPrefix[$columName];
    }


    /**
     * @return array
     */
    public function getColumnNamesWithPrefix()
    {
        if (!$this->columnNamesWithPrefix) {
            $this->fetchLazy();
        }

        return $this->columnNamesWithPrefix;
    }

    /**
     * fills in the column data
     */
    protected function fetchLazy()
    {
        if ($this->columnData && $this->columnNamesWithPrefix && $this->columnNames){
            return;
        }
        $this->columnNames = [];
        $this->columnNamesWithPrefix = [];
        foreach ($this->getColumnData() as $value) {
            $this->columnNamesWithPrefix[$value['Field']] = "`$this->prefix`.`{$value['Field']}`";
            $this->columnNames[] = $value['Field'];
        }
    }


    /**
     * @return array
     */
    public function getColumnNames()
    {
        if (!$this->columnNames) {
            $this->getColumnData();
            $this->fetchLazy();
        }

        return $this->columnNames;
    }

    /**
     * @param array $columnNames
     *
     * @return $this
     */
    public function setColumnsNames($columnNames)
    {
        $this->columnNames = $columnNames;

        return $this;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getColumnData()
    {
        if (!$this->columnData) {
            $this->fetchTableMetaData();
        }

        return $this->columnData;
    }

    /**
     * @param array $columnData
     *
     * @return $this
     */
    public function setColumnData($columnData)
    {
        $this->columnData = $columnData;

        return $this;
    }
}