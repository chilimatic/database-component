<?php
namespace chilimatic\lib\Database\Sql\Mysql\Querybuilder\Strategy;

use chilimatic\lib\Database\Sql\Querybuilder\Strategy\AbstractStrategy;
use chilimatic\lib\Database\Sql\Querybuilder\Strategy\GeneratorTrait;

/**
 * Class MySQLDeleteStrategy
 * @package chilimatic\lib\Database\sql\mysql\querybuilder\strategy
 */
class MySQLDeleteStrategy extends AbstractStrategy {
    /**
     * trait for generation of code
     */
    use GeneratorTrait;

    /**
     * @return string
     */
    public function generateSQLStatement()
    {
        return implode(' ', [
            $this->generateDeleteClause($this->tableData),
            $this->generateWhereClause($this->generateKeyList())
        ]);
    }
}