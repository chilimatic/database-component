<?php

namespace chilimatic\lib\Database\Sql\Mysql\Querybuilder\Strategy;

use chilimatic\lib\Database\Sql\Querybuilder\Strategy\AbstractStrategy;
use chilimatic\lib\Database\Sql\Querybuilder\Strategy\GeneratorTrait;

/**
 * Class MySQLUpdateStrategy
 * @package chilimatic\lib\Database\Sql\Mysql\Querybuilder\Strategy
 */
class MySQLUpdateStrategy extends AbstractStrategy
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
            $this->generateUpdateClause($this->tableData),
            $this->generateSetClause($this->generateFieldList()),
            $this->generateWhereClause($this->generateKeyList()),
        ]);
    }
}