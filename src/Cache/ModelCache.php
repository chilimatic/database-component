<?php
namespace chilimatic\lib\Database\Cache;
use chilimatic\lib\Database\Cache\Storage\ModelStorage;
use chilimatic\lib\Database\Sql\Orm\AbstractModel;


/**
 * Class ModelCache
 * @package chilimatic\Lib\Database\Cache\Handler
 */
class ModelCache
{
    /**
     * @var ModelStorage
     */
    private $modelStorage;

    /**
     * ModelCache constructor.
     */
    public function __construct()
    {
        $this->modelStorage = new \SplObjectStorage();
    }

    /**
     * @param AbstractModel $model
     * @param null $param
     */
    public function set(AbstractModel $model, $param = null)
    {
        if (!$this->modelStorage->contains($model)) {
            $this->modelStorage->attach($model);
        }
    }

    /**
     * removes the maybe existing object from the storage
     *
     * @param AbstractModel $model
     *
     * @return void
     */
    public function remove(AbstractModel $model) {
        $this->modelStorage->detach($model);
    }

    /**
     * @param AbstractModel $model
     * @param null $param
     *
     * @return AbstractModel|null
     */
    public function get(AbstractModel $model, $param = null)
    {
        $this->modelStorage->rewind();

        foreach ($this->modelStorage as $storedModel) {
            if ($storedModel === $model) {
                return $model;
            }
        }

        return null;
    }

}