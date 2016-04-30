<?php

namespace chilimatic\lib\Database\Mysql\Querybuilder\Strategy;

use chilimatic\lib\Database\Sql\Mysql\Querybuilder\Meta\MySQLTableData;

/**
 * Interface IMySQLStrategy
 * @package chilimatic\lib\Database\Mysql\Querybuilder\Strategy
 */
Interface IMySQLStrategy
{
    /**
     * @param MySQLTableData $tableData
     * @param array $modelData
     */
    public function __construct(MySQLTableData $tableData = null, array $modelData = null);

    /**
     * @return mixed
     */
    public function __toString();

    /**
     * @return string
     */
    public function generateSQLStatement();
}