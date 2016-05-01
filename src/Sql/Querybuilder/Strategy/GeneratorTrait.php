<?php
namespace chilimatic\lib\Database\Sql\Querybuilder\Strategy;

use chilimatic\lib\Database\Exception\QueryBuilderException;
use chilimatic\lib\Database\Sql\Querybuilder\Meta\AbstractSQLTableData;
use chilimatic\lib\interfaces\IFlyWeightTransformer;

/**
 * Class GeneratorTrait
 * @package chilimatic\lib\Database\Sql\Querybuilder\Strategy
 */
Trait GeneratorTrait
{

    /**
     * @var IFlyWeightTransformer
     */
    protected $transformer;

    /**
     * @param IFlyWeightTransformer $transformer
     *
     * @return $this
     */
    public function setTransformer(IFlyWeightTransformer $transformer) {
        $this->transformer = $transformer;
        return $this;
    }

    /**
     * @return IFlyWeightTransformer|null
     */
    public function getTransformer() {
        return $this->transformer;
    }


    /**
     * @param AbstractSQLTableData $tableData
     *
     * @return string
     */
    public function generateColumList(AbstractSQLTableData $tableData)
    {
        return implode(',', $tableData->getColumnNamesWithPrefix());
    }

    /**
     * @param AbstractSQLTableData $tableData
     *
     * @return string
     */
    public function generateSelectClause(AbstractSQLTableData $tableData)
    {
        return ("SELECT {$this->generateColumList($tableData)} FROM {$tableData->getTableNameWithPrefix()}");
    }

    /**
     * @param AbstractSQLTableData $tableData
     *
     * @return string
     */
    public function generateInsertClause(AbstractSQLTableData $tableData) {
        return "INSERT INTO {$tableData->getTableName()}";
    }

    /**
     * @param AbstractSQLTableData $tableData
     *
     * @return string
     */
    public function generateDeleteClause(AbstractSQLTableData $tableData) {
        return "DELETE FROM {$tableData->getTableName()}";
    }

    /**
     * @param AbstractSQLTableData $tableData
     *
     * @return string
     */
    public function generateUpdateClause(AbstractSQLTableData $tableData) {
        return "UPDATE {$tableData->getTableName()}";
    }

    /**
     * @param $fieldList
     *
     * @return string
     */
    public function generateSetClause($fieldList) {
        return "SET " .  implode(', ', $this->generatePredicateList($fieldList));
    }

    /**
     * @param $fieldList
     *
     * @return string
     */
    public function generateWhereClause($fieldList) {
        $predicateList = $this->generatePredicateList($fieldList);

        return ($predicateList) ? "WHERE " . implode(' AND ', $predicateList) : '';
    }

    /**
     * @param array $fieldList
     *
     * @return string
     */
    public function generatePredicateList(array $fieldList)
    {
        if (!$this->transformer) {
            throw new QueryBuilderException('Missing transformer for generic ids in SQL!');
        }

        $predicateList = [];
        $queryParam = $this->getQueryParam();

        foreach ($fieldList as $name)
        {

            if (!empty($queryParam[$name]) && is_array($queryParam[$name])) {
                $tName = $this->transformer->transform($name);
                $predicate = "$name IN (";

                for ($i = 0, $c = count($queryParam[$name]); $i < $c; $i++) {
                    $predicate .= $tName. $i . ',';
                }
                $predicate = rtrim($predicate, ',');
                $predicate .= ")";
                $predicateList[] = $predicate;
            } else {
                $predicateList[] = "$name = " . $this->transformer->transform($name);
            }
        }

        return $predicateList;
    }
}