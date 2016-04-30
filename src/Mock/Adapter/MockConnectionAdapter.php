<?php

namespace chilimatic\lib\Database\Mock\Adapter;
use chilimatic\lib\Database\Connection\IDatabaseConnectionAdapter;
use chilimatic\lib\Database\Connection\IDatabaseConnectionSettings;

/**
 * Class MockConnectionAdapter
 * @package chilimatic\lib\Database\Mock\Adapter
 */
class MockConnectionAdapter implements IDatabaseConnectionAdapter {
    /**
     * @var IDatabaseConnectionSettings
     */
    private $connectionSettings;

    /**
     * @var null
     */
    private $resource;

    /**
     * @var null
     */
    private $result;

    /**
     * MockConnectionAdapter constructor.
     * @param IDatabaseConnectionSettings $connectionSettings
     */
    public function __construct(IDatabaseConnectionSettings $connectionSettings)
    {
        $this->setConnectionSettings($connectionSettings);
    }

    /**
     * 
     */
    public function initResource()
    {
        $this->resource = true;
    }

    /**
     * @return IDatabaseConnectionSettings
     */
    public function getConnectionSettings()
    {
        return $this->connectionSettings;
    }

    /**
     * @param IDatabaseConnectionSettings $connectionSettings
     *
     * @return $this
     */
    public function setConnectionSettings(IDatabaseConnectionSettings $connectionSettings)
    {
        $this->connectionSettings = $connectionSettings;

        return $this;
    }

    /**
     * @return null
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param null $resource
     *
     * @return $this
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return [];
    }

    /**
     * @param $result
     *
     * @return $this
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }


}
