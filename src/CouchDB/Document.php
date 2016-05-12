<?php
namespace toool\Database\CouchDB;
use toool\Traits\JsonPropertyExportTrait;

/**
 * Class Document
 * @package toool\Database\CouchDB
 */
class Document extends AbstractDatabaseInterface implements \JsonSerializable
{
    use JsonPropertyExportTrait;

    /**
     * @var Database
     */
    private $database;

    /**
     * @JSON(export=true)
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $contentHash;

    /**
     * @var string
     */
    private $rawData;

    /**
     * @JSON(export=true)
     * @var string
     */
    private $revision;

    /**
     * @JSON(export=true)
     * @var string
     */
    private $id;

    /**
     * @JSON(export=true)
     * @var mixed
     */
    private $data;

    /**
     * @JSON(export=true)
     * @var array
     */
    private $attachmentList = [];

    /**
     * @var []
     */
    private $revisionList = [
        'start' => null,
        'ids'   => []
    ];

    /**
     * Document constructor.
     * @param string $name
     * @param Connection $connection
     * @param Database $database
     */
    public function __construct($name, Connection $connection, Database $database)
    {
        $this->name = $name;

        $this->database = $database;
        parent::__construct($connection);
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "{$this->getDatabase()}/{$this->name}";
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->__toString();
    }


    /**
     * @param array $querySet
     * @return bool
     */
    public function load(array $querySet = [])
    {
        if (!$this->name) {
            return false;
        }

        $query = $this->getQueryString(
            $this->getSource(),
            $querySet
        );

        $response = $this->get(
            $query,
            $this->getHeaders(),
            $this->getOptions()
        );


        if (!$response || $response->getStatusCode() != 200) {
            return false;
        }

        $this->rawData = trim($response->getBody(true));
        $tmp = json_decode($this->rawData, true);


        $this->assignData($tmp);

        if (isset($tmp['data'])) {
            $this->setContentHash(md5(json_encode($tmp['data'])));
        }

        return true;
    }

    /**
     * @param array $data
     */
    public function assignData(array $data)
    {
        if (!$data) {
            return;
        }

        // reduced set of assigned properties
        foreach ($data as $key => $value) {
            switch($key) {
                case '_id':
                    $this->id = $data[$key];
                    break;
                case '_rev':
                    $this->revision = $data[$key];
                    break;
                case '_revisions':
                    $this->revisionList = $data[$key];
                    break;
                case '_attachments':
                    $this->attachmentList = $data[$key];
                    break;
                case 'data':
                    $this->data = $data[$key];
                    break;
            }
        }

    }


    /**
     * @return bool
     */
    public function saveDocument()
    {
        if ($this->getContentHash() == md5(json_encode($this->data))) {
            return true;
        }

        if ($this->id) {
            $set['_id']  = $this->id;
        }

        if ($this->revision) {
            $set['_rev'] = $this->revision;
        }

        $set['data'] = $this->getData();


        $ret = $this->create(
            $this->getSource(),
            $this->getHeaders(),
            json_encode($set),
            $this->getOptions()
        );


        if ($ret) {
            // set the new revision
            $response = $this->getConnection()->getResponse()->getBody(true);
            $tmp = json_decode($response, true);
            $this->revision = $tmp['rev'];
            $this->id = $tmp['id'];
        }

        return $set;
    }


    /**
     * @param string $key
     * @param string $value
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getContentHash()
    {
        return $this->contentHash;
    }

    /**
     * @param string $contentHash
     */
    public function setContentHash($contentHash)
    {
        $this->contentHash = $contentHash;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * @return string
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * @return mixed
     */
    public function getRevisionList()
    {
        return $this->revisionList;
    }
}