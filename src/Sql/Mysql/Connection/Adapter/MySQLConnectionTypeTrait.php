<?php
namespace chilimatic\lib\Database\Sql\Mysql\Connection\Adapter;

/**
 * Class MySQLConnectionTypeTrait
 * @package chilimatic\lib\Database\Sql\Mysql\Connection\Adapter
 */
Trait MySQLConnectionTypeTrait
{
    /**
     * simple check if it's a socket connection or not
     * (can be set manual as well)
     *
     * @return void
     */
    protected function findConnectionType()
    {
        static $reflection;

        if (!$reflection) {
            $reflection = new \ReflectionClass(get_class($this));
        }

        if (!$reflection->getMethod('getConnectionSettings')) {
            $this->setSocket(false);
            return;
        }

        $connectionSetting = $this->getConnectionSettings();

        if (!$connectionSetting) {
            $this->setSocket(false);
            return;
        }

        switch (true) {
            /**
             * Unix only setting the localhost will try to connect directly through a domainsocket
             */
            case ($connectionSetting->getHost() === 'localhost' && stripos(PHP_OS, 'win') === false):
                $this->setSocket(true);
                break;
            /**
             * if it's a path we can asume it's a socket
             */
            case (strpos($connectionSetting->getHost(), '/') === 0):
                $this->setSocket(true);
                break;
            /**
             * everything else is a TCP connection
             */
            default:
                $this->setSocket(false);
                break;
        }
    }
}