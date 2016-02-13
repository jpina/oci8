<?php

namespace Jpina\Oci8;

/**
 * Class PersistentConnection
 * @package Jpina\Oci8
 * @see http://php.net/manual/en/book.oci8.php
 */
class Oci8PersistentConnection extends Oci8Connection
{
    /**
     * Connect to an Oracle database using a persistent connection
     *
     * New instances of this class will use the same connection across multiple requests.
     *
     * @param string $username
     * @param string $password
     * @param string $connectionString
     * @param string $characterSet
     * @param int $sessionMode
     * @return resource
     * @see http://php.net/manual/en/function.oci-pconnect.php
     */
    protected function connect(
        $username,
        $password,
        $connectionString = null,
        $characterSet = null,
        $sessionMode = null
    ) {
        set_error_handler($this->getErrorHandler());
        $connection = oci_pconnect($username, $password, $connectionString, $characterSet, $sessionMode);
        restore_error_handler();
        return $connection;
    }
}
