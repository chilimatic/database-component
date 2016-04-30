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

/**
 * Class AbstractModel
 *
 * @package chilimatic\lib\Database\orm
 */
abstract class AbstractModel implements \JsonSerializable
{

    /**
     * @return mixed
     */
    abstract public function jsonSerialize();
}