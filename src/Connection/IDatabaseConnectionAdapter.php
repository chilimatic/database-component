<?php
namespace chilimatic\lib\Database\Connection;

/**
 * Interface IDatabaseConnectionAdapter
 * @package chilimatic\lib\Database\Connection
 */
interface IDatabaseConnectionAdapter {

    /**
     * @param IDatabaseConnectionSettings $connectionSettings
     */
    public function __construct(IDatabaseConnectionSettings $connectionSettings);

    /**
     * @return mixed
     */
    public function initResource();

    /**
     * @return mixed
     */
    public function getResource();

    /**
     * @param $resource
     *
     * @return mixed
     */
    public function setResource($resource);

    /**
     * @return mixed
     */
    public function getResult();

    /**
     * @param $result
     * @return mixed
     */
    public function setResult($result);
}