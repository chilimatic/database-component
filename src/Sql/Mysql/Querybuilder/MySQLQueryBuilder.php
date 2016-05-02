<?php
namespace chilimatic\lib\Database\Sql\Mysql\Querybuilder;

use chilimatic\lib\Cache\Engine\ICache;
use chilimatic\lib\Database\AbstractDatabase;
use chilimatic\lib\Database\Sql\Mysql\Querybuilder\Meta\MySQLTableData;
use chilimatic\lib\Database\Sql\Mysql\Querybuilder\Strategy\MySQLDeleteStrategy;
use chilimatic\lib\Database\Sql\Mysql\Querybuilder\Strategy\MySQLInsertStrategy;
use chilimatic\lib\Database\Sql\Mysql\Querybuilder\strategy\MySQLSelectStrategy;
use chilimatic\lib\Database\Sql\Mysql\Querybuilder\Strategy\MySQLUpdateStrategy;
use chilimatic\lib\Database\Model\AbstractModel;
use chilimatic\lib\Database\Sql\Querybuilder\AbstractQueryBuilder;
use chilimatic\lib\Database\Sql\Querybuilder\ConsistencyTrait;
use chilimatic\lib\Database\Sql\Querybuilder\Meta\AbstractSQLTableData;
use chilimatic\lib\Interfaces\IFlyWeightTransformer;
use chilimatic\lib\Transformer\String\DynamicSQLParameter;


/**
 * Class MySQLQueryBuilder
 * @package chilimatic\lib\Database\Sql\Mysql\Querybuilder
 */
class MySQLQueryBuilder extends AbstractQueryBuilder
{
    const UPDATE_QUERY = 2;
    const INSERT_QUERY = 1;
    const DELETE_QUERY = 3;
    const SELECT_QUERY = 0;

    /**
     * trait for the annotation checks
     */
    use ConsistencyTrait;

    /**
     * orm table mapping field
     */
    const TABLE_INDEX = 'table';

    /**
     * this is the property where the relations are
     * stored as a json object
     *
     * @var string
     */
    const RELATION_PROPERTY = "fieldMapping";

    /**
     * init cache connection
     *
     * @param ICache $cache
     * @param AbstractDatabase $db
     */
    public function __construct(ICache $cache, AbstractDatabase $db = null)
    {
        $this->relation       = new \SplFixedArray();
        $this->modelDataCache = [];

        $this->tableData = new MySQLTableData($db);
        $this->paramTransformer = new DynamicSQLParameter();
        parent::__construct($cache, $db);
    }


    /**
     * @param AbstractModel $model
     * @param array $param
     *
     * @return string
     */
    public function generateSelectForModel(AbstractModel $model, $param)
    {
        $cacheData = $this->fetchCacheData($model);
        if (isset($cacheData[self::RELATION_INDEX])) {
            $this->checkRelations($cacheData[self::RELATION_INDEX]);
        }

        /**
         * select Strategy
         */
        $strategy = new MySQLSelectStrategy(
            $cacheData[self::TABLE_DATA_INDEX],
            array_keys($param),
            $param
        );

        $strategy->setTransformer($this->paramTransformer);

        return  [
            $strategy->generateSQLStatement(),
            $param,
            self::SELECT_QUERY
        ];
    }

    /**
     * @param AbstractModel $model
     * @param MySQLTableData $tableData
     *
     * @return array
     */
    public function prepareModelData(AbstractModel $model, MySQLTableData $tableData)
    {
        $data    = $columData = [];
        $keyList = $tableData->getPrimaryKey();
        $reflection        = new \ReflectionClass($model);

        foreach ($tableData->getColumnNames() as $column) {

            try {
                $reflectedProperty = $reflection->getProperty($column);


                $reflectedProperty->setAccessible(true);

                $columData = [
                    'value' => $reflectedProperty->getValue($model),
                    'name'  => $column
                ];

                if (in_array($column, $keyList, false)) {
                    array_merge($columData, ['KEY' => true]);
                }
                $data[] = $columData;
            } catch (\Exception $e) {

                $logger = \chilimatic\lib\di\ClosureFactory::getInstance()->get('error-log');
                $logger->error($e->getMessage(), $e->getTraceAsString());
            }
        }

        return $data;
    }

    /**
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function generateInsertForModel(AbstractModel $model)
    {
        $cacheData = $this->fetchCacheData($model);

        $strategy = new MySQLInsertStrategy(
            $cacheData[self::TABLE_DATA_INDEX],
            $this->prepareModelData(
                $model,
                $cacheData[self::TABLE_DATA_INDEX]
            )
        );

        $strategy->setTransformer($this->paramTransformer);


        return [
            $strategy->generateSQLStatement(),
            $this->prepareModelDataForStatement($strategy->getModelData()),
            self::INSERT_QUERY
        ];
    }

    /**
     * @param $modelData
     *
     * @return array
     */
    public function prepareModelDataForStatement($modelData)
    {
        $newModelData = [];
        foreach ($modelData as $column) {
            $name = $this->paramTransformer->transform($column['name']);

            switch (true) {
                case is_object($column['value']):
                    switch (true) {
                        case ($column['value'] instanceOf \DateTime):
                            $newModelData[] = [$name, (string) $column['value']->format('Y-m-d H:i:s')];
                            break;
                    }

                    break;
                default:
                    $newModelData[] = [$name, (string) $column['value']];
                    break;
            }

        }

        return $newModelData;
    }


    /**
     * @param AbstractModel $model
     * @param null $diff
     *
     * @return array
     */
    public function generateUpdateForModel(AbstractModel $model, $diff = null)
    {
        $cacheData = $this->fetchCacheData($model);
        $strategy  = new MySQLUpdateStrategy(
            $cacheData[self::TABLE_DATA_INDEX],
            $this->prepareModelData(
                $model,
                $cacheData[self::TABLE_DATA_INDEX]
            )
        );

        $strategy->setTransformer($this->paramTransformer);

        return [
            $strategy->generateSQLStatement(),
            $this->prepareModelDataForStatement($strategy->getModelData()),
            self::UPDATE_QUERY
        ];
    }

    /**
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function generateDeleteForModel(AbstractModel $model)
    {
        $cacheData = $this->fetchCacheData($model);

        $strategy = new MySQLDeleteStrategy(
            $cacheData[self::TABLE_DATA_INDEX],
            $this->prepareModelData(
                $model,
                $cacheData[self::TABLE_DATA_INDEX]
            )
        );

        $strategy->setTransformer($this->paramTransformer);

        return [
            $strategy->generateSQLStatement(),
            $this->prepareModelDataForStatement($strategy->getPrimaryKeySet()),
            self::DELETE_QUERY
        ];
    }

    /**
     * @return \SplFixedArray
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * @param \SplFixedArray $relation
     *
     * @return $this
     */
    public function setRelation(\SplFixedArray $relation)
    {
        $this->relation = $relation;

        return $this;
    }

    /**
     * @return \chilimatic\lib\Cache\Engine\ICache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param \chilimatic\lib\Cache\Engine\ICache $cache
     *
     * @return $this
     */
    public function setCache(ICache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * @return array
     */
    public function getModelDataCache()
    {
        return $this->modelDataCache;
    }

    /**
     * @param array $modelDataCache
     *
     * @return $this
     */
    public function setModelDataCache($modelDataCache)
    {
        $this->modelDataCache = $modelDataCache;

        return $this;
    }

    /**
     * @return AbstractSQLTableData
     */
    public function getTableData()
    {
        return $this->tableData;
    }

    /**
     * @param AbstractSQLTableData $tableData
     *
     * @return $this
     */
    public function setTableData(AbstractSQLTableData $tableData)
    {
        $this->tableData = $tableData;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     *
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return IFlyWeightTransformer
     */
    public function getParamTransformer()
    {
        return $this->paramTransformer;
    }

    /**
     * @param IFlyWeightTransformer $paramTransformer
     *
     * @return $this
     */
    public function setParamTransformer(IFlyWeightTransformer $paramTransformer)
    {
        $this->paramTransformer = $paramTransformer;

        return $this;
    }

}