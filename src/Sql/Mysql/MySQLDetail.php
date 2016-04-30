<?php

namespace chilimatic\lib\Database\Sql\Mysql;

/**
 * Class MySQLDetail
 * @package chilimatic\lib\Database\Sql\Mysql
 */
class MySQLDetail
{
    /**
     * mysql object
     *
     * @var object
     */
    private $db = null;

    /**
     * character set
     *
     * @var string
     */
    public $character_set_database = '';

    /**
     * constructor
     *
     * @param MySQL $db
     */
    public function __construct(MySQL $db)
    {

        if (empty($db))  {
            return;
        }

        $this->db = $db;
        $this->init();
    }


    /**
     * method that gets all the mysql database
     * settings an fills them into properties of
     * this object
     *
     */
    private function init()
    {

        if (empty($this->db)) {
            return false;
        }

        $sql = (string)"SHOW VARIABLES";
        $res = $this->db->query((string)$sql);

        if (!$res) {
            return false;
        }

        $data = $this->db->fetch_assoc_list($res);
        if (!empty($data)) {
            foreach ($data as $variable) {
                $this->$variable[(string)'Variable_name'] = (string)$variable['Value'];
            }
            $this->db->free($res);
        }

        // clear all the unecessary variables and the endles recursion of the db
        unset($this->db, $data, $variable);

        return true;
    }
}