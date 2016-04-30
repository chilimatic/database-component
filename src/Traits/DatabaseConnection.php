<?php
namespace chilimatic\lib\Database\Traits;

use chilimatic\lib\Database\Exception\DatabaseException;
use chilimatic\lib\Database\sql\mysql\connection\MySQLConnection;
use chilimatic\lib\Database\Sql\Mysql\Connection\MySQLConnectionSettings;
use chilimatic\lib\Database\Sql\Mysql\MySQL;

/**
 * Class Database
 * @package chilimatic\lib\traits
 */
trait DatabaseConnection
{
    /**
     * default database type
     *
     * @var string
     */
    private $defaultDatabaseType = 'mysql';

    /**
     * current database type
     *
     * @var string
     */
    private $database_type = '';

    /**
     * Database Object
     *
     * @var MySQL
     */
    public $db;


    /**
     * initializes the database Object if necessary
     *
     *
     * @param null $param
     *
     * @throws \chilimatic\lib\Database\Exception\DatabaseException|\Exception
     * @return boolean
     */
    protected function __init_database($param = null)
    {

        if ($this->defaultDatabaseType === '') {
            $this->defaultDatabaseType = $param['default_database_type'];
        }

        if (isset($param['type']) && is_string($param['type'])) {
            $this->database_type = $param['type'];
        } else {
            $this->database_type = $this->defaultDatabaseType;
        }


        switch (true) {
            case ($this->db instanceof MySQL):
                return true;
        }

        try {
            switch ($this->database_type) {
                case 'mysql':
                    $connection = new MySQLConnectionSettings(
                        $param['db_host'],
                        $param['db_user'],
                        $param['db_pass'],
                        isset($param['db_name']) ? $param['db_name'] : null,
                        isset($param['db_port']) ? $param['db_port'] : null
                    );

                    $masterParam = new MySQLConnection($connection);
                    $this->db = new Mysql($masterParam);
                    break;
            }

        } catch (DatabaseException $e) {
            throw $e;
        }

        return true;
    }


    /**
     * destroys the current database object
     *
     *
     * @return boolean
     */
    protected function __clean_database()
    {
        $this->db = null;

        return true;
    }
}