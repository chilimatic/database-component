<?php
namespace toool\Database\CouchDB;

/**
 * Class Database
 * @package toool\Database\CouchDB
 */
class Database extends AbstractDatabaseInterface
{
    const ALL_DBS = '_all_dbs';

    /**
     * @var string
     */
    private $name;

    /**
     * @var []
     */
    private $status;


    /**
     * Database constructor.
     * @param string $databaseName
     * @param Connection $connection
     */
    public function __construct($databaseName, Connection $connection)
    {
        $this->name = $databaseName;
        parent::__construct($connection);
    }

    /**
     * @return bool|\Guzzle\Http\Message\EntityEnclosingRequestInterface|\Guzzle\Http\Message\RequestInterface
     */
    public function createDb()
    {
        if (!$this->getSource()) {
            return false;
        }

        return $this->create(
            $this->getSource(),
            $this->getHeaders(),
            null,
            $this->getOptions()
        );
    }

    /**
     * @return bool
     */
    public function load(array $querySet = [])
    {
        if (!$this->getSource()) {
            return false;
        }

        $path = $this->getQueryString(
            $this->getSource(),
            $querySet
        );

        $response = $this->get($path);

        if (!$response) {
            return false;
        }

        $body = $response->getBody(true);

        if ($body) {
            $this->status = json_decode($body, true);
        }

        return true;
    }

    /**
     * @return array|null|string
     */
    public function getAll()
    {
        $response = $this->get(self::ALL_DBS);

        $responseCode = $response->getStatusCode();
        if ($responseCode < 200 || $responseCode > 300) {
            return null;
        }

        return json_decode($response->getBody(true));
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->__toString();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "/$this->name/";
    }
}