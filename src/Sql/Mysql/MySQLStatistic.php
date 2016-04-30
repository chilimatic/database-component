<?php
namespace chilimatic\lib\Database\Sql\Mysql;


use \chilimatic\lib\Database\Exception\DatabaseException;

/**
 * Class MySQLStatistic
 * @package chilimatic\lib\Database\Sql\Mysql
 */
class MySQLStatistic extends MySQL
{

    /**
     * disk usage
     *
     * @var array
     */
    public $diskUsage = '';


    /**
     * amount of table
     *
     * @var int
     */
    public $tableAmount = '';


    /**
     * size of table
     *
     * @var int
     */
    public $tableSize = '';


    /**
     * db object
     *
     * @var MySQL
     */
    public $db;

    
    /**
     * get processlist
     *
     * @throws DatabaseException
     * @return mixed:
     */
    public function getProcessList()
    {

        try {
            if (!$this->db) {
                throw new DatabaseException(__METHOD__ . 'No Database Object has been given', MySQL::ERR_NO_CREDENTIALS, MySQL::SEVERITY_LOG, __FILE__, __LINE__);
            }

            $sql = (string)"SHOW FULL PROCESSLIST";
            $res = $this->db->query($sql);

            if (empty($res)) {
                return [];
            }

            return $this->db->fetchObjectList($res);
        } catch (DatabaseException $e) {
            throw $e;
        }
    }

    /**
     * show table listing
     *
     * @throws DatabaseException
     *
     * @return array:
     */
    public function showTableList()
    {

        try {
            if (!$this->db) {
                throw new DatabaseException(__METHOD__ . 'No Database Object has been given', MySQL::ERR_NO_CREDENTIALS, MySQL::SEVERITY_LOG, __FILE__, __LINE__);
            }

            $sql = (string)"SELECT * FROM `information_schema`.`tables`";
            $res = $this->db->query($sql);

            if (empty($res)) {
                return [];
            }

            return $this->db->fetchObjectList($res);

        } catch (DatabaseException $e) {
            throw $e;
        }
    }
}