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
     * @param null|int $port
     * @param array $settingList
     */
    public function __construct($host, $username, $password, $database = null, $port = null, array $settingList = []);

    /**
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string|null $database
     * @param null|int $port
     * @param array $settingList
     * @return mixed
     */
    public function setConnectionParam($host, $username, $password, $database = null, $port = null, array $settingList = []);
}