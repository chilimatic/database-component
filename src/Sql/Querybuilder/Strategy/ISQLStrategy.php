<?php
namespace chilimatic\lib\Database\Sql\Querybuilder\Strategy;
use chilimatic\lib\Database\Sql\Querybuilder\Meta\AbstractSQLTableData;

/**
 * Interface ISQLStrategy
 *
 * @package chilimatic\lib\Database\mysql\querybuilder\strategy
 */
Interface ISQLStrategy
{
    /**
     * @param AbstractSQLTableData $tableData
     * @param array $modelData
     */
    public function __construct(AbstractSQLTableData $tableData = null, array $modelData = null);

    /**
     * @return mixed
     */
    public function __toString();

    /**
     * @return string
     */
    public function generateSQLStatement();
}