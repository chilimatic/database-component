<?php
namespace chilimatic\lib\Database\Model;

/**
 * Interface IModel
 * @package chilimatic\lib\Database\Model
 */
Interface IModel extends \JsonSerializable
{

    /**
     * @return mixed
     */
     public function jsonSerialize();
}