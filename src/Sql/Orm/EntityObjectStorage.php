<?php
namespace chilimatic\lib\Database\Sql\Orm;

class EntityObjectStorage extends \SplObjectStorage implements \JsonSerializable
{
    /**
     * @var \ReflectionClass
     */
    private $reflection;

    /**
     * keep them cached
     *
     * @var []
     */
    private $columnMap;

    /**
     * @param string $columnName
     *
     * @return array
     */
    public function getAsArray($columnName = null)
    {
        if ($columnName) {
            $this->rewind();
            $obj = $this->current();
            if ($obj) {
                if (!$this->reflection) {
                    $this->reflection = new \ReflectionClass($this->current());
                }

                if ($this->columnMap === null) {
                    $this->columnMap = [];
                    foreach ($this->reflection->getProperties() as $property) {
                        $this->columnMap[$property->getName()] = $property;
                    }
                }
            }
        }


        $this->rewind();
        $arr = [];
        while ($this->valid()) {
            if ($columnName && !empty($this->columnMap[$columnName])) {
                $obj = $this->current();
                /**
                 * @var \ReflectionProperty $test
                 */
                $this->columnMap[$columnName]->setAccessible(true);
                $arr[] = $this->columnMap[$columnName]->getValue($obj);
            } else {
                $arr[] = $this->current();
            }

            $this->next();
        }

        return $arr;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->getAsArray();
    }
}