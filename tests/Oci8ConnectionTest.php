<?php

namespace Jpina\Oci8\Test;

use Jpina\Oci8\Oci8Connection;
use Jpina\Oci8\Oci8Exception;

class Oci8ConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jpina\Oci8\Oci8Connection
     */
    protected static $connection;

    /**
     * @var \Jpina\Oci8\Oci8Connection
     */
    protected static $closedConnection;

    public static function setUpBeforeClass()
    {
        static::$closedConnection = static::getNewConnection();
        static::$closedConnection->close();
        static::$connection = static::getNewConnection();
    }

    public static function tearDownAfterClass()
    {
        static::$connection->close();
    }

    public static function getNewConnection()
    {
        return new Oci8Connection(
            getenv('DB_USER'),
            getenv('DB_PASSWORD'),
            '//' . getenv('DB_HOST') . ':' . getenv('DB_PORT') . '/' . getenv('DB_SCHEMA'),
            getenv('DB_CHARSET'),
            OCI_DEFAULT
        );
    }

    public function getConnection()
    {
        return static::$connection;
    }

    public function getClosedConnection()
    {
        return static::$closedConnection;
    }

    public function testCanConnect()
    {
        $database = static::getNewConnection();

        $this->assertInstanceOf('\Jpina\Oci8\Oci8ConnectionInterface', $database);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     * @expectedExceptionMessage oci_new_connect(): ORA-12541: TNS:no listener
     * @expectedExceptionCode 12541
     */
    public function testCannotConnect()
    {
        new Oci8Connection('no_user', 'no_password', '//localhost:1521/XE');
    }

    public function testCanChangePassword()
    {
        $database = $this->getConnection();
        $isChanged = $database->changePassword(getenv('DB_USER'), getenv('DB_PASSWORD'), 'temp_password');
        $this->assertTrue($isChanged);
        $isChanged = $database->changePassword(getenv('DB_USER'), 'temp_password', getenv('DB_PASSWORD'));
        $this->assertTrue($isChanged);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     * @expectedExceptionMessage ORA-01017: invalid username/password; logon denied
     * @expectedExceptionCode 1017
     */
    public function testCannotChangePassword()
    {
        $database = $this->getConnection();
        $database->changePassword('no_user', 'no_password', 'temp_password');
    }

    public function testCanCloseConnection()
    {
        $database = static::getNewConnection();
        $isSuccess = $database->close();
        $this->assertTrue($isSuccess);
    }

    public function testCannotCloseConnection()
    {
        $database = $this->getClosedConnection();
        $isSuccess = $database->close();
        $this->assertFalse($isSuccess);
    }

    public function testCanCommit()
    {
        $database = $this->getConnection();
        $isSuccess = $database->commit();
        $this->assertTrue($isSuccess);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     * @expectedExceptionMessage oci_commit() expects parameter 1 to be resource, null given
     */
    public function testCannotCommit()
    {
        $database = $this->getClosedConnection();
        $isSuccess = $database->commit();
        $this->assertFalse($isSuccess);
    }

    public function testCanCopyLob()
    {
        $this->markTestIncomplete();
        $database = $this->getConnection();
        $sql = "SELECT EMPTY_BLOB() AS lob_from, EMPTY_BLOB() AS lob_to FROM SYS.DUAL";
        $statement = $database->parse($sql);
        $statement->execute();
        $row = $statement->fetchAssoc();
        /** @var \OCI_Lob $lobFrom */
        $lobFrom = $row['LOB_FROM'];
        $lobFrom->writeTemporary('LOB CONTENTS');
        $lobFrom->rewind();
        /** @var \OCI-Lob $lobTo */
        $lobTo = $row['LOB_TO'];

        $isSuccess = $database->copyLob($lobTo, $lobFrom);
        $this->assertTrue($isSuccess);
    }

    public function testCannotCopyLob()
    {
        $database = $this->getConnection();
        $sql = "SELECT EMPTY_BLOB() AS lob_from, EMPTY_BLOB() AS lob_to FROM SYS.DUAL";
        $statement = $database->parse($sql);
        $statement->execute();
        $row = $statement->fetchAssoc();
        /** @var \OCI_Lob $lobFrom */
        $lobFrom = $row['LOB_FROM'];
        $lobFrom->writeTemporary('LOB CONTENTS');
        $lobFrom->rewind();
        /** @var \OCI-Lob $lobTo */
        $lobTo = $row['LOB_TO'];

        $database = $this->getClosedConnection();
        $isSuccess = $database->copyLob($lobTo, $lobFrom);
        $this->assertFalse($isSuccess);
    }

    public function testFreeDescriptor()
    {
        $database = $this->getConnection();
        $descriptor = $database->getNewDescriptor();
        $isSuccess = $database->freeDescriptor($descriptor);
        $this->assertTrue($isSuccess);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     * @expectedExceptionMessage oci_free_descriptor() expects parameter 1 to be OCI-Lob, null given
     */
    public function testCannotFreeDescriptor()
    {
        $database = $this->getConnection();
        $database->freeDescriptor(null);
    }

    public function testGetClientMayorVersion()
    {
        $database = $this->getConnection();
        $mayorVersion = $database->getClientMayorVersion();
        $this->assertTrue(is_int($mayorVersion));
        $this->assertTrue($mayorVersion > 0);
    }

    public function testGetClientVersion()
    {
        $database = $this->getConnection();
        $clientVersion = $database->getClientVersion();

        $this->assertRegExp('/^\\d+(\\.\\d+)+$/', $clientVersion);
    }

    public function testGetError()
    {
        $database = $this->getConnection();
        try {
            $this->testCannotChangePassword();
        } catch (Oci8Exception $ex){
            //
        }
        $error = $database->getError();
        $this->assertTrue(is_array($error));

        $this->assertArrayHasKey('code', $error);
        $this->assertArrayHasKey('message', $error);
        $this->assertArrayHasKey('offset', $error);
        $this->assertArrayHasKey('sqltext', $error);
    }

    public function testGetEmptyError()
    {
        $database = $this->getNewConnection();
        $error = $database->getError();
        $this->assertFalse($error);
    }

    public function testGetNewCollection()
    {
        $database = $this->getConnection();
        $sql = 'CREATE OR REPLACE TYPE TEST_ARRAY AS VARRAY(100) OF VARCHAR2(20)';
        $statement = $database->parse($sql);
        $statement->execute();
        $collection = $database->getNewCollection('TEST_ARRAY');
        $this->assertInstanceOf('\OCI-Collection', $collection);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     * @expectedExceptionMessage oci_new_collection(): OCI-22303: type ""."NOT_FOUND_COLLECTION" not found
     * @expectedExceptionCode 22303
     */
    public function testCannotGetNewCollection()
    {
        $database = $this->getConnection();
        $database->getNewCollection('NOT_FOUND_COLLECTION');
    }

    public function testGetNewCursor()
    {
        $database = $this->getConnection();
        $cursor = $database->getNewCursor();
        $this->assertInstanceOf('\Jpina\Oci8\Oci8CursorInterface', $cursor);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     * @expectedExceptionMessage oci_new_cursor() expects parameter 1 to be resource, null given
     */
    public function testCannotGetNewCursor()
    {
        $database = $this->getClosedConnection();
        $database->getNewCursor();
    }

    public function testGetNewDescriptor()
    {
        $database = $this->getConnection();
        $descriptor = $database->getNewDescriptor();
        $this->assertInstanceOf('\OCI-Lob', $descriptor);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     * @expectedExceptionMessage oci_new_descriptor() expects parameter 1 to be resource, null given
     */
    public function testCannotGetNewDescriptor()
    {
        $database = $this->getClosedConnection();
        $database->getNewDescriptor();
    }

    public function testGetServerMayorVersion()
    {
        $database = $this->getConnection();
        $mayorVersion = $database->getServerMayorVersion();
        $this->assertTrue(is_int($mayorVersion));
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     * @expectedExceptionMessage oci_server_version() expects parameter 1 to be resource, null given
     */
    public function testCannotGetServerMayorVersion()
    {
        $database = $this->getClosedConnection();
        $database->getServerMayorVersion();
    }

    public function testGetServerVersion()
    {
        $database = $this->getConnection();
        $serverVersion = $database->getServerVersion();
        $this->assertTrue(is_string($serverVersion));
        $this->assertTrue(strlen($serverVersion) > 0);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     * @expectedExceptionMessage oci_server_version() expects parameter 1 to be resource, null given
     */
    public function testCannotGetServerVersion()
    {
        $database = $this->getClosedConnection();
        $database->getServerVersion();
    }

    public function testIsLobEqual()
    {
        $database = $this->getConnection();
        $sql = "SELECT EMPTY_BLOB() AS lob_1, EMPTY_BLOB() AS lob_2 FROM SYS.DUAL";
        $statement = $database->parse($sql);
        $statement->execute();
        $row = $statement->fetchAssoc();
        /** @var \OCI_Lob $lob1 */
        $lob1 = $row['LOB_1'];
        /** @var \OCI-Lob $lob2 */
        $lob2 = $row['LOB_2'];

        $isEqual = $database->isLobEqual($lob1, $lob2);
        $this->assertTrue($isEqual);
    }

    public function testIsLobNotEqual()
    {
        $database = $this->getConnection();
        $sql = "SELECT EMPTY_BLOB() AS lob_1, EMPTY_BLOB() AS lob_2 FROM SYS.DUAL";
        $statement = $database->parse($sql);
        $statement->execute();
        $row = $statement->fetchAssoc();
        /** @var \OCI_Lob $lob1 */
        $lob1 = $row['LOB_1'];
        /** @var \OCI-Lob $lob2 */
        $lob2 = $row['LOB_2'];
        $lob2->writeTemporary('asdasd');

        $isEqual = $database->isLobEqual($lob1, $lob2);
        $this->assertFalse($isEqual);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     * @expectedExceptionMessage oci_lob_is_equal() expects parameter 1 to be OCI-Lob, string given
     */
    public function testIsLobEqualException()
    {
        $database = $this->getConnection();
        $isEqual = $database->isLobEqual('abc', 'def');
        $this->assertFalse($isEqual);
    }

    public function testParse()
    {
        $database = $this->getConnection();
        $sql = 'SELECT * FROM SYS.DUAL';

        $statement = $database->parse($sql);
        $this->assertInstanceOf('Jpina\Oci8\Oci8StatementInterface', $statement);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     * @expectedExceptionMessage oci_parse() expects parameter 1 to be resource, null given
     */
    public function testCannotParse()
    {
        $database = $this->getClosedConnection();
        $sql = 'SELECT * FROM SYS.DUAL';
        $database->parse($sql);
    }

    public function testSetAction()
    {
        $database = $this->getConnection();
        $isSuccess = $database->setAction('Friend Lookup');
        $this->assertTrue($isSuccess);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     * @expectedExceptionMessage oci_set_action() expects parameter 1 to be resource, null given
     */
    public function testCannotSetAction()
    {
        $database = $this->getClosedConnection();
        $database->setAction('Friend Lookup');
    }

    public function testSetClientIdentifier()
    {
        $database = $this->getConnection();
        $isSuccess = $database->setClientIdentifier('whoami');
        $this->assertTrue($isSuccess);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     * @expectedExceptionMessage oci_set_client_identifier() expects parameter 1 to be resource, null given
     */
    public function testCannotSetClientIdentifier()
    {
        $database = $this->getClosedConnection();
        $database->setClientIdentifier('whoami');
    }

    public function testSetClientInfo()
    {
        $database = $this->getConnection();
        $isSuccess = $database->setClientInfo('My Application Version 2');
        $this->assertTrue($isSuccess);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     * @expectedExceptionMessage oci_set_client_info() expects parameter 1 to be resource, null given
     */
    public function testCannotSetClientInfo()
    {
        $database = $this->getClosedConnection();
        $database->setClientInfo('My Application Version 2');
    }

    public function testSetEdition()
    {
        $database = $this->getConnection();
        $edition = 'TEST_EDITION_' . time();
        $statement = $database->parse('CREATE EDITION ' . $edition);
        $statement->execute();

        $isSuccess = $database->setEdition($edition);
        $this->assertTrue($isSuccess);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     * @expectedExceptionMessage oci_set_edition() expects parameter 1 to be string, object given
     */
    public function testCannotSetEdition()
    {
        $database = $this->getClosedConnection();
        $isSuccess = $database->setEdition($database);
        $this->assertFalse($isSuccess);
    }

    public function testSetInternalDebug()
    {
        $database = $this->getConnection();
        $database->setInternalDebug(true);
    }

    public function testSetModule()
    {
        $database = $this->getConnection();
        $isSuccess = $database->setModuleName('Home Page');
        $this->assertTrue($isSuccess);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     * @expectedExceptionMessage oci_set_module_name() expects parameter 1 to be resource, null given
     */
    public function testCannotSetModule()
    {
        $database = $this->getClosedConnection();
        $database->setModuleName('Home Page');
    }

    public function testRollback()
    {
        $database = $this->getConnection();
        $isSuccess = $database->rollback();
        $this->assertTrue($isSuccess);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     * @expectedExceptionMessage oci_rollback() expects parameter 1 to be resource, null given
     */
    public function testCannotRollback()
    {
        $database = $this->getClosedConnection();
        $database->rollback();
    }
}
