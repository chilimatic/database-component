<?php
namespace chilimatic\lib\Database\Sql\Connection;

use chilimatic\lib\Database\Connection\IDatabaseConnectionSettings;
use chilimatic\lib\Interfaces\ISelfValidator;
use chilimatic\lib\Validator\Traits\PropertyValidatorGeneratorTrait;

/**
 * Class AbstractSqlConnectionSettings
 *
 * @package chilimatic\lib\Database\sql\connection
 */
abstract class AbstractSQLConnectionSettings implements IDatabaseConnectionSettings, ISQLConnectionSettings, ISelfValidator
{
    /**
     * trait implements the ISelfValidator
     */
    use PropertyValidatorGeneratorTrait;

    /**
     * @validator (name="Type\Scalar\IsString")
     * @validator (name="Generic\NotEmpty")
     *
     * the host ip
     *
     * @var string
     */
    private $host;

    /**
     * @validator (name="Type\Scalar\IsString")
     * @validator (name="Generic\NotEmpty")
     *
     * the username
     *
     * @var string
     */
    private $username;

    /**
     * @validator (name="Type\Scalar\IsString")
     * @validator (name="Generic\NotEmpty")
     * the password
     *
     * @var string
     */
    private $password;

    /**
     * @validator (name="Type\Scalar\IsString", mandatory="false")
     *
     * @var string
     */
    private $database;

    /**
     * @validator (name="Type\Scalar\IsInt", mandatory="false")
     *
     * @var int
     */
    private $port;

    /**
     * AbstractSQLConnectionSettings constructor.
     * @param string $host
     * @param string $username
     * @param string $password
     * @param null|string $database
     * @param null|string $port
     * @param array $settingList
     */
    public function __construct($host, $username, $password, $database = null, $port = null, array $settingList = [])
    {
       $this->setConnectionParam($host, $username, $password, $database, $port, $settingList);
    }

    /**
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string|null $database
     * @param null|int $port
     * @param array $settingList
     *
     * @return void
     */
    public function setConnectionParam($host, $username, $password, $database = null, $port = null, array $settingList = [])
    {
        $this->setHost($host);
        $this->setUsername($username);
        $this->setPassword($password);
        $this->setDatabase($database);
        $this->setPort($port);

        // call the specific implementation
        $this->setSettings($settingList);
    }

    /**
     * @param array $settingList
     *
     * @return mixed
     */
    abstract public function setSettings(array $settingList = []);

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     *
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param string $database
     *
     * @return $this
     */
    public function setDatabase($database)
    {
        $this->database = $database;

        return $this;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     *
     * @return $this
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }
}