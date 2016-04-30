<?php
namespace chilimatic\lib\Database\Sql\Mysql\Querybuilder\Strategy;

use chilimatic\lib\Database\Sql\Querybuilder\Strategy\AbstractStrategy;
use chilimatic\lib\Database\Sql\Querybuilder\Strategy\GeneratorTrait;


/**
 * Class MySQLInsertStrategy
 * @package chilimatic\lib\Database\Sql\Mysql\Querybuilder\Strategy
 */
class MySQLInsertStrategy extends AbstractStrategy
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
            $this->generateInsertClause($this->tableData),
            $this->generateSetClause($this->generateFieldList())
        ]);
    }
}