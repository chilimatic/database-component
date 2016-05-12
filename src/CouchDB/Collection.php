<?php
namespace toool\Database\CouchDB;


class Collection extends AbstractDatabaseInterface
{
    /**
     * this will automatically be added after the database name
     */
    const DOCUMENT_LIST = '_all_docs';

    /**
     * @var Database
     */
    private $database;

    /**
     * @var \SplObjectStorage
     */
    private $collection;

    /**
     * @var string
     */
    private $query;

    /**
     * @var
     */
    private $rows;

    /**
     * Collection constructor.
     * @param Connection $connection
     * @param Database $database
     */
    public function __construct(Connection $connection, Database $database)
    {
        $this->database = $database;
        $this->collection = new \SplObjectStorage();

        parent::__construct($connection);
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->getDatabase()->getSource() . self::DOCUMENT_LIST;
    }

    /**
     * @param array $querySet
     * @return bool|\SplObjectStorage
     */
    public function load(array $querySet = [])
    {

        $path = $this->getQueryString($this->getSource(), $querySet);

        $response = $this->get(
            $path,
            $this->getHeaders(),
            $this->getOptions()
        );

        if (!$response || $response->getStatusCode() != 200) {
            return false;
        }

        $rawData = trim($response->getBody(true));
        $tmp = json_decode($rawData, true);

        $this->rows = count($tmp['rows']);

        // clone is faster than the constructor
        $documentTpl = new Document(
            '',
            $this->getDatabase()->getConnection(),
            $this->getDatabase()
        );

        foreach ($tmp['rows'] as $documentData) {
            $newDoc = clone $documentTpl;
            $newDoc->setName($documentData['key']);
            $newDoc->assignData($documentData);
            $this->collection->attach($newDoc);
        }

        return true;
    }

    /**
     * @return Database
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param Database $database
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }

    /**
     * @return mixed
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param mixed $collection
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return mixed
     */
    public function getRows()
    {
        return $this->rows;
    }
}