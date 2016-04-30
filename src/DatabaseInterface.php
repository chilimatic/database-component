<?php
namespace chilimatic\lib\Database;

/**
 * Interface DatabaseInterface
 * @package chilimatic\lib\Database
 */
interface DatabaseInterface
{
    /**
     * @param string $query
     *
     * @return mixed
     */
    public function query($query);

    /**
     * @return mixed
     */
    public function lastQuery();

    /**
     * @param string $query
     *
     * @return mixed
     */
    public function execute($query);

    /**
     * @return mixed
     */
    public function prepare($query);
}