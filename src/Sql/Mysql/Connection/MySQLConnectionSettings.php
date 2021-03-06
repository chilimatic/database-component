<?php

namespace chilimatic\lib\Database\Sql\Mysql\Connection;
use chilimatic\lib\Database\Sql\Connection\AbstractSQLConnectionSettings;



/**
 * Class MySQLConnectionSettings
 * @package chilimatic\lib\Database\Sql\Mysql\Connection
 */
class MySQLConnectionSettings extends AbstractSQLConnectionSettings
{
    /**
     * Mysql default Port
     *
     * @validator (name="Type\Scalar\IsInt", expect="true")
     * @validator (name="Generic\NotEmpty")
     *
     * @var int
     */
    CONST MYSQL_DEFAULT_PORT = 3306;

    /**
     * connection type
     * @validator (name="Type\Scalar\IsBool", expect="true", mandatory="true")
     *
     * @var bool
     */
    private $persistent = false;

    /**
     * @validator (name="Type\Scalar\IsString", expect="true", operator="&", mandatory="false")
     *
     * @var string
     */
    private $charset;

    /**
     * @var array
     */
    private $options;

    /**
     * @param string $host
     * @param string $username
     * @param string $password
     * @param null|string $database
     * @param null|int $port
     * @param array $settingList
     */
    public function setConnectionParam($host, $username, $password, $database = null, $port = null, array $settingList = [])
    {
        parent::setConnectionParam($host, $username, $password, $database, $settingList,  $settingList);

        // if there is no port set the default port
        if (!$settingList && !$this->getPort()) {
            $this->setPort(self::MYSQL_DEFAULT_PORT);
        }
    }

    /**
     * @param array $settingList
     *
     * @return void
     */
    public function setSettings(array $settingList = [])
    {
        if (!$settingList) {
            return;
        }

        if (isset($settingList['persistent'])) {
            $this->setPersistent( (bool) $settingList['persistent']);
        }

        if (isset($settingList['charset'])) {
            $this->setCharset($settingList['charset']);
        }
    }

    /**
     * connectionDataIsSet
     * <p>
     * checks if at least all the needed parameters for a connection are set
     * </p>
     *
     * @return bool
     */
    public function connectionDataIsSet()
    {
        return ($this->getHost() && $this->getUsername() && $this->getPassword());
    }

    /**
     * @return boolean
     */
    public function isPersistent()
    {
        return $this->persistent;
    }

    /**
     * @param boolean $persistent
     *
     * @return $this
     */
    public function setPersistent($persistent)
    {
        $this->persistent = (bool) $persistent;

        return $this;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param string $charset
     *
     * @return $this
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * set PDO Options here
     *
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }
}