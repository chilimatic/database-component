<?php
namespace chilimatic\lib\Database\Connection;

/**
 * Interface IDatabaseConnection
 *
 * @package chilimatic\lib\Database\Connection
 */
interface IDatabaseConnection
{

    /**
     * int representations of
     * binary values for connection roles
     */
    const CONNECTION_ROLE_MASTER    = 1;
    const CONNECTION_ROLE_SLAVE     = 2;

    /**
     * amount of default reconnect tries
     *
     * @var int
     */
    const MAX_DEFAULT_RECONNECTS    = 3;

    /**
     * <p>
     *  since different databases need to implement different Connections this is a rather
     *  generic interface
     * </p>
     *
     * @param IDatabaseConnectionSettings $connectionSettings
     * @param string $adapterName
     */
     public function __construct(IDatabaseConnectionSettings $connectionSettings, $adapterName = '');

    /**
     * checks if the connectionSettings are valid
     *
     * @return bool
     */
    public function connectionSettingsAreValid();

    /**
     * prepares the different connection meta data so
     * the validator and other processes can be triggered
     *
     * @param IDatabaseConnectionSettings $connectionSettings
     * @param $adapterName
     * @return mixed
     */
    public function prepareAndInitializeAdapter(IDatabaseConnectionSettings $connectionSettings, $adapterName);


    /**
     * tries to reconnect to the database if the connection is lost
     *
     * @return mixed
     */
    public function reconnect();
}