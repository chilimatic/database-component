<?php
namespace chilimatic\lib\Database\Sql\Orm;

use chilimatic\lib\Database\Cache\ModelCache;
use chilimatic\lib\Database\AbstractDatabase;
use chilimatic\lib\Database\ErrorLogTrait;
use chilimatic\lib\Database\Model\AbstractModel;
use chilimatic\lib\Database\Sql\Mysql\Querybuilder\MySQLQueryBuilder;
use chilimatic\lib\Database\Sql\Querybuilder\AbstractQueryBuilder;
use chilimatic\lib\Interfaces\IFactoryOptions;
use chilimatic\lib\Transformer\TransformerFactory;


/**
 * Class EntityManager
 * @package chilimatic\lib\Database\Sql\Orm
 */
class EntityManager
{
    /**
     * @trait
     */
    use ErrorLogTrait;

    /**
     * @var AbstractDatabase
     */
    public $db;

    /**
     * @var \chilimatic\lib\Database\Sql\Querybuilder\AbstractQueryBuilder
     */
    public $queryBuilder;

    /**
     * parameter if the objects within an object should be loaded
     *
     * @var bool
     */
    private $lazyLoading = true;

    /**
     * parameter for caching
     *
     * @var bool
     */
    private $useCache = true;


    /**
     * cache to store already queried data within
     * -> the models will be filled by it but not stored within it
     *
     * @var ModelCache
     */
    private $modelCache;

    /**
     * @var IFactoryOptions
     */
    private $transformerFactory;


    /**
     * @param AbstractDatabase $db
     * @param AbstractQueryBuilder $queryBuilder
     */
    public function __construct(AbstractDatabase $db, AbstractQueryBuilder $queryBuilder = null)
    {
        $this->db           = $db;
        $this->queryBuilder = $queryBuilder;
        $this->modelCache   = new ModelCache();
        $this->transformerFactory = new TransformerFactory();

        if ($this->queryBuilder) {
            $this->queryBuilder->setDb($db);
        }
    }

    /**
     * @param string $query
     * @param array $param
     *
     * @return \PDOStatement
     */
    protected function prepare($query, $param)
    {
        /**
         * @var \PdoStatement $stmt
         */
        $stmt = $this->db->prepare($query);

        foreach ($param as $set) {
            if (isset($set['value']) || isset($set['name'])) {
                $value = &$set['value'];
                $key   = $set['name'];
            } else {
                $value = &$set[1];
                $key   = $set[0];
            }

            if (!$key) {
                continue;
            }
            $stmt->bindParam($key, $value);
        }

        return $stmt;
    }

    /**
     * @param \PDOStatement $stmt
     * @param $model
     *
     * @return EntityObjectStorage
     */
    public function executeQuery($model, \PDOStatement $stmt)
    {
        $objectStorage = new EntityObjectStorage();
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 1) {
                return $this->getList($model, $stmt);
            }
            $objectStorage->attach($this->hydrate($model, $stmt->fetchObject()));

        } else {
            $this->log(E_ERROR, implode(',', $stmt->errorInfo()));
        }

        return $objectStorage;
    }


    /**
     * @param $stmt
     * @param $model
     *
     * @return EntityObjectStorage
     */
    public function getList($model, $stmt)
    {
        $container = new EntityObjectStorage();
        /**
         * @var $stmt \PDOStatement
         */
        while ($row = $stmt->fetchObject()) {
            $container->attach($this->hydrate(clone $model, $row));
        }

        return $container;
    }

    /**
     * this method is so the user can add the param list like this [ 'id' => 1, 'name' => 'bla' ]
     * the other strategies are built different because of the update statement
     *
     * @param array $param
     *
     * @return array
     */
    public function prepareParam(array $param = null)
    {
        if (!$param) {
            return [];
        }
        $ret = [];

        $transformer = $this->transformerFactory->make('\\String\\DynamicSQLParameter', []);
        
        foreach ($param as $key => $value) {
            if (is_array($value)) {
                $keyString = $transformer($key);
                foreach ($value as $subKey => $subValue) {
                    $ret[] =[$keyString.$subKey , $subValue];
                }
            } else {
                $ret[] = [$transformer($key), $value];
            }

        }

        return $ret;
    }

    /**
     * @param AbstractModel $model
     * @param $param
     *
     * @return EntityObjectStorage
     */
    public function findBy(AbstractModel $model, $param = [])
    {
        if ($this->useCache && $this->modelCache) {
            if (($ret = $this->modelCache->get($model, $param))) {
                return $ret;
            }
        }

        list($query, $param, $type) = $this->queryBuilder->generateSelectForModel($model, $param);

        $result = $this->executeQuery(
            $model,
            $this->prepare(
                $query,
                $this->prepareParam($param)
            )
        );

        if ($this->useCache && $this->modelCache) {
            $this->modelCache->set($model, $param);
        }

        return $result;
    }

    /**
     * @param AbstractModel $model
     * @param $param
     *
     * @return AbstractModel
     */
    public function findOneBy(AbstractModel $model, $param = [])
    {

        $result = $this->findBy($model, $param);
        $result->rewind();

        return $result->current();
    }

    /**
     * @param AbstractModel $model
     * @param mixed $row
     *
     * @return AbstractModel
     */
    public function hydrate(AbstractModel $model, $row)
    {
        if (!$row) {
            return $model;
        }

        $p = (array) get_object_vars($row);
        $reflection = new \ReflectionClass($model);
        foreach ($p as $property => $value) {
            try {
                $property = $reflection->getProperty($property);
                $property->setAccessible(true);
                $property->setValue($model, $value);
            } catch (\Exception $e) {
                $this->log(E_ERROR, $e->getMessage());
                continue;
            }
        }

        $this->hydrateRelations($model);

        return $model;
    }

    /**
     * @param AbstractModel $model
     *
     * @return AbstractModel
     */
    public function hydrateRelations(AbstractModel $model)
    {
        if (!$this->queryBuilder->fetchCacheData($model)) {
            return $model;
        }
        $reflection = new \ReflectionClass($model);

        foreach ($this->queryBuilder->fetchCacheData($model)['relationList'] as $relation) {
            $injectionModel = new $relation['model']();
            try {
                $property = $reflection->getProperty($relation['target']);
            } catch (\Exception $e) {
                $this->log(E_ERROR, $e->getMessage());
                continue;
            }

            $property->setAccessible(true);

            /**
             * @todo implement dynamic mappings from doc head
             */
            if (!$this->lazyLoading) {
                $mappingProperty = $reflection->getProperty($relation['mapping_id']);
                $mappingProperty->setAccessible(true);
                $this->findOneBy($injectionModel, ['id' => $mappingProperty->getValue($model)]);
                unset($mappingProperty);
            }
            $property->setValue($model, $injectionModel);
        }

        return $model;
    }

    /**
     * @param AbstractModel $model
     *
     * @return bool
     */
    public function delete(AbstractModel $model)
    {

        list($query, $param, $type) = $this->queryBuilder->generateDeleteForModel($model);

        if ($this->modelCache->get($model)) {
            $this->modelCache->remove($model);
        }
        
        $stmt = $this->prepare($query, $param);

        if ($stmt->execute()) {
            return true;

        } else {
            $this->log(E_ERROR, print_r($stmt->errorInfo(), true));
            return false;
        }
    }

    /**
     * @param array $modelCacheData
     * @param $model
     *
     * @return bool
     */
    public function checkForPrimaryKey(array $modelCacheData, $model)
    {
        $className = get_class($model);
        $transformer = $this->transformerFactory->make('\\String\\UnderScoreToCamelCase', []);

        foreach ($modelCacheData[$className]['tableData']->getPrimaryKey() as $key)
        {
            if (strpos($key, '_') !== false){
                $getter = "get". ucfirst($transformer($key));
            } else {
                $getter = "get". ucfirst($key);
            }

            if (method_exists($model, $getter) && $model->{$getter}() !== null) {
                return true;
            }
        }
        return false;
    }


    /**
     * @param AbstractModel $model
     * @param bool $forceInsert
     *
     * @todo think about a way to simplify this I'm sure I overcomplicated the approach based on the code structure
     * (class ...) the issue is the Responsibility <- the check should not be in the entity manager and not
     * in the cache layer
     *
     * @return mixed
     */
    public function getPersistData(AbstractModel $model, $forceInsert = false)
    {
        if ($forceInsert || !$this->modelCache->get($model)) {
            return $this->queryBuilder->generateInsertForModel($model);
        }

        $modelCacheData = $this->queryBuilder->getModelDataCache();

        if (isset($modelCacheData[get_class($model)])) {
            $hasPrimaryKeys = $this->checkForPrimaryKey($modelCacheData, $model);

            if ($hasPrimaryKeys) {
                return $this->queryBuilder->generateUpdateForModel($model);
            } else {
                return $this->queryBuilder->generateInsertForModel($model);
            }
        }

        return $this->queryBuilder->generateUpdateForModel($model);
    }


    /**
     * @param AbstractModel $model
     * @param bool|false $forceInsert (force flag)
     *
     * @return bool
     */
    public function persist(AbstractModel $model, $forceInsert = false)
    {
        $retval = false;
        try {
            list ($query, $param, $type) = $this->getPersistData($model, $forceInsert);
            $stmt = $this->prepare($query, $param);

            if ($stmt->execute()) {

                if ($type == MySQLQueryBuilder::INSERT_QUERY) {
                    $this->setModelPrimaryKey($this->db->getLastInsertId(), $model);
                }

                $retval = true;
            } else {
                $this->log(E_ERROR, print_r($stmt->errorInfo(), true));
            }
        } catch (\Exception $e) {
            $this->log(E_ERROR, $e->getMessage(), true);
            return false;
        }

        // add to the modelCache
        $this->modelCache->set($model);

        return $retval;
    }

    /**
     * @param $keyValue
     * @param AbstractModel $model
     */
    public function setModelPrimaryKey($keyValue, AbstractModel $model)
    {
        if (!$keyValue) {
            return;
        }
        $modelCacheData = $this->queryBuilder->getModelDataCache();

        if (isset($modelCacheData[get_class($model)])) {
            foreach ($modelCacheData[get_class($model)]['tableData']->getPrimaryKey() as $primaryKey) {
                $setter = "set". ucfirst($primaryKey);
                if (method_exists($model, $setter)) {
                    $model->{$setter}($keyValue);
                }
            }
        }
    }

    /**
     * @param AbstractQueryBuilder $queryBuilder
     *
     * @return $this
     */
    public function setQueryBuilder(AbstractQueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;

        return $this;
    }

    /**
     * @return AbstractQueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * @param $lazyLoading
     *
     * @return $this
     */
    public function setLazyLoading($lazyLoading)
    {
        $this->lazyLoading = (bool)$lazyLoading;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getLazyLoading()
    {
        return $this->lazyLoading;
    }

    /**
     * @param $db
     *
     * @return $this
     */
    public function setDb($db)
    {
        $this->db = $db;

        return $this;
    }

    /**
     * @return \chilimatic\lib\Database\AbstractDatabase
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @return boolean
     */
    public function isUseCache()
    {
        return $this->useCache;
    }

    /**
     * @param boolean $useCache
     *
     * @return $this
     */
    public function setUseCache($useCache)
    {
        $this->useCache = (bool)$useCache;

        return $this;
    }

    /**
     * @return IFactoryOptions
     */
    public function getTransformerFactory()
    {
        return $this->transformerFactory;
    }

    /**
     * @param IFactoryOptions $transformerFactory
     */
    public function setTransformerFactory(IFactoryOptions $transformerFactory = null)
    {
        $this->transformerFactory = $transformerFactory;
    }
}