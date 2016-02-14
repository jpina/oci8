<?php

namespace Jpina\Oci8\Test;

use Jpina\Oci8\Oci8PersistentConnection;

class Oci8PersistentConnectionTest extends \PHPUnit_Framework_TestCase
{
    public static function getNewConnection()
    {
        return new Oci8PersistentConnection(
            getenv('DB_USER'),
            getenv('DB_PASSWORD'),
            '//' . getenv('DB_HOST') . ':' . getenv('DB_PORT') . '/' . getenv('DB_SCHEMA'),
            getenv('DB_CHARSET'),
            OCI_DEFAULT
        );
    }

    public function testCanConnect()
    {
        $database = static::getNewConnection();

        $this->assertInstanceOf('\Jpina\Oci8\Oci8ConnectionInterface', $database);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     * @expectedExceptionMessage oci_pconnect(): ORA-12541: TNS:no listener
     * @expectedExceptionCode 12541
     */
    public function testCannotConnect()
    {
        new Oci8PersistentConnection('no_user', 'no_password', '//localhost:1521/XE');
    }
}
