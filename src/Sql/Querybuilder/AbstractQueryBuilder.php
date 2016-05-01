<?php
/**
 *
 * @author j
 * Date: 12/23/14
 * Time: 2:32 PM
 *
 * File: abstractquerybuilder.class.php
 */
namespace chilimatic\lib\Database\Sql\Querybuilder;

use chilimatic\lib\Cache\Engine\ICache;
use chilimatic\lib\Database\AbstractDatabase;
use chilimatic\lib\Database\Cache\Handler\ModelCache;
use chilimatic\lib\Database\sql\orm\AbstractModel;
use chilimatic\lib\Database\Sql\Querybuilder\Meta\AbstractSQLTableData;
use chilimatic\lib\Interfaces\IFlyWeightTransformer;
use chilimatic\lib\Parser\Annotation\AnnotationOrmParser;

/**
 * Class AbstractQueryBuilder
 *
 * @package chilimatic\lib\Database\orm
 */
abstract class AbstractQueryBuilder implements IQueryBuilder
{
    /**
     * @var string
     */
    const TABLE_DATA_INDEX  = 'tableData';
    const RELATION_INDEX    = 'relationList';


    /**
     * @var AnnotationOrmParser
     */
    protected $parser;

    /**
     * @var \PDO
     */
    protected $db;


    /**
     * @var ModelCache
     */
    protected $cache;

    /**
     * @var array
     */
    protected $modelDataCache;

    /**
     * @var AbstractSQLTableData
     */
    protected $tableData;

    /**
     * @var $string
     */
    protected $position;

    /**
     * @var IFlyWeightTransformer
     */
    protected $paramTransformer;

    /**
     * @var array
     */
    protected $relation;

    /**
     * constructor
     *
     * @param ICache $cache
     * @param AbstractDatabase $db
     */
    public function __construct(ICache $cache = null, AbstractDatabase $db)
    {
        $this->parser = new AnnotationOrmParser();
        $this->cache  = $cache;
    }

    /**
     * @param \ReflectionClass $reflection
     *
     * @return mixed|string
     */
    public function parseTableName(\ReflectionClass $reflection)
    {
        $hd = $this->parser->parse($reflection->getDocComment());

        if (!empty($hd[0])) {
            return $hd[1];
        }

        $table = mb_substr($reflection->getName(), mb_strlen($reflection->getNamespaceName()));

        return mb_strtolower(str_replace('\\', '', $table));
    }

    /**
     * @param AbstractModel $model
     *
     * @return array
     */
    public function fetchCacheData(AbstractModel $model)
    {
        $this->position = get_class($model);
        if (!isset($this->modelDataCache[$this->position])) {
            $this->modelDataCache[$this->position] = $this->prepareCacheData($model);
        }

        return $this->modelDataCache[$this->position];
    }

    /**
     * @param AbstractModel $model
     *
     * @return array
     */
    public function prepareCacheData(AbstractModel $model)
    {
        /**
         * @todo change MySQLTableData to a usecase based one
         */
        $tableData  = clone $this->tableData;
        $reflection = new \ReflectionClass($model);
        $tableData->setTableName($this->parseTableName($reflection));

        return [
            'tableData'    => $tableData,
            'reflection'   => new \ReflectionClass($model),
            'relationList' => $this->extractRelations($reflection),
        ];
    }


    /**
     * @param \ReflectionClass $reflection
     *
     * @return bool|\SplFixedArray
     */
    public function extractRelations(\ReflectionClass $reflection)
    {
        $propertyList = $reflection->getDefaultProperties();

        if ($this->cache && $res = $this->cache->get(md5(json_encode($propertyList)))) {
            return $res;
        }

        $relation = [];

        foreach ($propertyList as $name => $value) {
            $comment = $reflection->getProperty($name)->getDocComment();
            $d = $this->parser->parse($comment);
            if (!$d) {
                continue;
            }
            $relation[] = [
                'mapping_id' => $d[0],
                'model'      => $d[1],
                'target'     => $name
            ];
        }

        if ($this->cache) {
            $this->cache->set(md5(json_encode($propertyList)), $relation, 300);
        }

        return $relation;
    }

    /**
     * @return AnnotationOrmParser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * @param AnnotationOrmParser $parser
     *
     * @return $this
     */
    public function setParser($parser)
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * @return \PDO
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @param AbstractDatabase $db
     *
     * @return $this
     */
    public function setDb(AbstractDatabase $db)
    {
        $this->db = $db;

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

}