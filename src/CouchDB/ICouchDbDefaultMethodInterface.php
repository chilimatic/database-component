<?php

namespace toool\Database\CouchDB;

/**
 * Interface ICouchDbDefaultMethodInterface
 * @package toool\Database\CouchDB
 */
interface ICouchDbDefaultMethodInterface
{

    /**
     * @param string $name
     * @param array $headers
     * @param array $options
     * @return mixed
     */
    public function get($name, $headers = [], $options = []);

    /**
     * @param string $name
     * @param array $headers
     * @param null $body
     * @param array $options
     * @return mixed
     */
    public function create($name, $headers = [], $body = null, $options = []);

    /**
     * @param string $name
     * @param array $headers
     * @param null $body
     * @param array $options
     * @return mixed
     */
    public function delete($name, $headers = [], $body = null, $options = []);

    /**
     * @param string $name
     * @param array $headers
     * @param null $body
     * @param array $options
     * @return mixed
     */
    public function update($name, $headers = [], $body = null, $options = []);


    /**
     * @param string $name
     * @param array $headers
     * @param null $body
     * @param array $options
     * @return mixed
     */
    public function save($name, $headers = [], $body = null, $options = []);


    /**
     * @param string $name
     * @param array $headers
     * @param array $options
     * @return mixed
     */
    public function info($name, $headers = [], $options = []);

}