<?php
namespace chilimatic\lib\Database\Sql\Connection;

/**
 * Interface ISQLConnectionSettings
 * @package chilimatic\lib\Database\Sql\Connection
 */
interface ISQLConnectionSettings {

    /**
     * @param string $host
     * @param string $username
     * @param string $password
     * @param null|string $database
     * @param array $settingList
     */
    public function __construct($host, $username, $password, $database = null, $settingList = []);

    /**
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string|null $database
     * @param array $settingList
     *
     * @return mixed
     */
    public function setConnectionParam($host, $username, $password, $database = null, $settingList = []);
}