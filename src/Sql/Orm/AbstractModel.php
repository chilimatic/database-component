<?php
/**
 *
 * @author j
 * Date: 12/23/14
 * Time: 2:36 PM
 *
 * File: abstractmodel.class.php
 */
namespace chilimatic\lib\Database\Sql\Orm;
use chilimatic\lib\Database\Model\IModel;

/**
 * Class AbstractModel
 *
 * @package chilimatic\lib\Database\orm
 */
abstract class AbstractModel implements IModel
{

    /**
     * @return mixed
     */
    abstract public function jsonSerialize();
}