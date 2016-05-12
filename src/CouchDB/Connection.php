<?php
namespace toool\Database\CouchDB;

/**
 * Class Connection
 * @package toool\Database\CouchDB
 */
class Connection
{
    /**
     * @var \Guzzle\Http\Client
     */
    protected $client;

    /**
     * @var \Guzzle\Http\Message\Request
     */
    protected $request;

    /**
     * @var \Guzzle\Http\Message\Response
     */
    protected $response;

    /**
     * @var \SplQueue
     */
    protected $errorQueue;

    /**
     * Connection constructor.
     * @param \Guzzle\Http\Client $client
     */
    public function __construct(\Guzzle\Http\Client $client)
    {
        $this->client = $client;
        $this->errorQueue = new \SplQueue();
    }

    /**
     * @param string $uri
     * @param array $headers
     * @param array $options
     * @return array|\Guzzle\Http\Message\Response|null
     */
    public function get($uri, $headers = [], $options = [])
    {
        if (!$uri) {
            return null;
        }

        $this->request = $this->client->get($uri, $headers, $options);
        $this->response = $this->request->send();


        return $this->response;
    }


    /**
     * @param string $uri
     * @param array $headers
     * @param array $body
     * @param array $options
     * @return \Guzzle\Http\Message\EntityEnclosingRequestInterface|\Guzzle\Http\Message\RequestInterface
     */
    public function put($uri, $headers = [], $body = null, $options = [])
    {
        $this->request = $this->client->put($uri, $headers, $body, $options);
        $this->response = $this->request->send();

        return $this->response;
    }

    /**
     * @param string $uri
     * @param array $headers
     * @param array $body
     * @param array $options
     * @return \Guzzle\Http\Message\EntityEnclosingRequestInterface|\Guzzle\Http\Message\RequestInterface
     */
    public function delete($uri, $headers = [], $body = null, $options = [])
    {
        $this->request = $this->client->delete($uri, $headers, $body, $options);
        $this->response = $this->request->send();

        return $this->response;
    }

    /**
     * @param string $uri
     * @param array $headers
     * @param array $options
     * @return \Guzzle\Http\Message\RequestInterface
     */
    public function head($uri, $headers = null, $options = [])
    {
        $this->request = $this->client->head($uri, $headers, $options);
        $this->response = $this->request->send();

        return $this->response;
    }


    /**
     * @param string $uri
     * @param array $headers
     * @param string $postBody
     * @param array $options
     * @return array|\Guzzle\Http\Message\Response
     */
    public function post($uri, $headers = [], $postBody = null, $options = [])
    {
        $this->request = $this->client->post($uri, $headers, $postBody, $options);
        $this->response = $this->request->send();

        return $this->response;
    }


    /**
     * @param $e
     * @return bool
     */
    public function handleException(\Guzzle\Http\Exception\ClientErrorResponseException $e)
    {
        $response = $e->getResponse();
        $this->errorQueue->enqueue($response->getBody(true));

        return false;
    }

    /**
     * @return \Guzzle\Http\Message\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param \Guzzle\Http\Message\Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return \Guzzle\Http\Message\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param \Guzzle\Http\Message\Response $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return \SplQueue
     */
    public function getErrorQueue()
    {
        return $this->errorQueue;
    }

    /**
     * @param \SplQueue $errorQueue
     */
    public function setErrorQueue(\SplQueue $errorQueue)
    {
        $this->errorQueue = $errorQueue;
    }
}