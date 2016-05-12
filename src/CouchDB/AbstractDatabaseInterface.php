<?php
namespace toool\Database\CouchDB;

/**
 * Class AbstractDatabaseInterface
 * @package toool\Database\CouchDB
 */
abstract class AbstractDatabaseInterface implements ICouchDbDefaultMethodInterface
{
    /**
     * @var []
     */
    private $headers = [];

    /**
     * @var []
     */
    private $options = [];

    /**
     * @var Connection
     */
    private $connection;

    /**
     * Database constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return string
     */
    abstract public function getSource();

    /**
     * @param array $querySet
     * @return bool
     */
    abstract public function load(array $querySet = []);


    /**
     * @param string $name
     * @param array $headers
     * @param array $options
     * @return \http\Client\Response|false
     */
    public function get($name, $headers = [], $options = [])
    {
        try {
            $response = $this->getConnection()->get($name, $headers, $options);

            if ($response->getStatusCode() == 200) {
                return $response;
            }

            return false;
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
            return $this->getConnection()->handleException($e);
        }
    }

    /**
     * @param string $name
     * @param array $headers
     * @param null $body
     * @param array $options
     * @return \Guzzle\Http\Message\EntityEnclosingRequestInterface|\Guzzle\Http\Message\RequestInterface
     */
    public function create($name, $headers = [], $body = null, $options = [])
    {
        try {
            $response = $this->getConnection()->put($name, $headers, $body, $options);

            if ($response->getStatusCode() == 201) {
                return true;
            }

            return false;
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
            return $this->getConnection()->handleException($e);
        }
    }

    /**
     * @param string $name
     * @param array $headers
     * @param null $body
     * @param array $options
     * @return \Guzzle\Http\Message\EntityEnclosingRequestInterface|\Guzzle\Http\Message\RequestInterface
     */
    public function delete($name, $headers = [], $body = null, $options = [])
    {
        try {
            $response = $this->getConnection()->delete($name, $headers, $body, $options);

            if ($response->getStatusCode() == 200) {
                return true;
            }

            return false;
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
            return $this->getConnection()->handleException($e);
        }
    }

    /**
     * @param string $name
     * @param array $headers
     * @param null $body
     * @param array $options
     * @return \Guzzle\Http\Message\EntityEnclosingRequestInterface|\Guzzle\Http\Message\RequestInterface
     */
    public function update($name, $headers = [], $body = null, $options = [])
    {
        try {
            $response = $this->getConnection()->put($name, $headers, $body, $options);

            if ($response->getStatusCode() == 200) {
                return true;
            }

            return false;
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
            return $this->getConnection()->handleException($e);
        }
    }

    /**
     * @param string $name
     * @param array $headers
     * @param array $options
     * @return bool|\Guzzle\Http\Message\RequestInterface
     */
    public function info($name, $headers = [], $options = [])
    {
        try {
            return $this->getConnection()->head($name, $headers, $options);
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
            return $this->getConnection()->handleException($e);
        }
    }

    /**
     * @param string $path
     * @param array $querySet
     * @return string
     */
    public function getQueryString($path, array $querySet)
    {
        if (!$querySet) {
            return $path;
        }

        $tmpSet = [];
        foreach ($querySet as $name => $param) {
            switch($name)
            {
                case 'include_docs':
                case 'revs':
                    $tmpSet[] = (string) $name . '=' . (string) ($param ? 'true' : 'false');
                    break;
                default:
                    $tmpSet[] .= (string) $name . '=' . (string) $param;
                    break;
            }
        }

        return $path . '?' . implode('&', $tmpSet);
    }



    /**
     * @param string $name
     * @param array $headers
     * @param null $body
     * @param array $options
     * @return \Guzzle\Http\Message\EntityEnclosingRequestInterface|\Guzzle\Http\Message\RequestInterface
     */
    public function save($name, $headers = [], $body = null, $options = [])
    {
        return $this->update($name, $headers, $body, $options);
    }




    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param Connection $connection
     */
    public function setConnection(Connection $connection = null)
    {
        $this->connection = $connection;
    }

    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param mixed $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param mixed $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }
}