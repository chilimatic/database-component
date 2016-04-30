<?php
namespace chilimatic\lib\Database\Sql\Mysql\Querybuilder\Strategy;

use chilimatic\lib\Database\Sql\Querybuilder\Strategy\AbstractStrategy;
use chilimatic\lib\Database\Sql\Querybuilder\Strategy\GeneratorTrait;

/**
 * Class MySQLSelectStrategy
 * @package chilimatic\lib\Database\Sql\Mysql\Querybuilder\Strategy
 */
class MySQLSelectStrategy extends AbstractStrategy
{
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
            $this->generateSelectClause($this->tableData),
            $this->generateWhereClause($this->modelData),
        ]);
    }
}