<?php

namespace Jpina\Oci8\Test;

use Jpina\Oci8\Oci8Connection;
use Jpina\Oci8\Oci8Exception;
use Jpina\Oci8\Oci8Statement;

class Oci8StatementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jpina\Oci8\Oci8ConnectionInterface
     */
    protected static $database;

    public static function setUpBeforeClass()
    {
        static::$database = new Oci8Connection(
            getenv('DB_USER'),
            getenv('DB_PASSWORD'),
            '//' . getenv('DB_HOST') . ':' . getenv('DB_PORT') . '/' . getenv('DB_SCHEMA'),
            getenv('DB_CHARSET'),
            OCI_DEFAULT
        );
    }

    /**
     * @return \Jpina\Oci8\Oci8StatementInterface
     */
    protected function getNewStatement($sql = '')
    {
        $sql = empty($sql) ? 'SELECT * FROM SYS.DUAL' : $sql;
        return static::$database->parse($sql);
    }

    /**
     * @return \Jpina\Oci8\Oci8StatementInterface
     */
    protected function getClosedStatement()
    {
        $statement = $this->getNewStatement();
        $statement->free();
        return $statement;
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     * @expectedExceptionMessage resource is not an oci8 statement
     */
    public function testCannotCreateNewStatement()
    {
        new Oci8Statement('');
    }

    public function testCanBindArrayByName()
    {
        $create = 'DROP TABLE bind_example';
        $statement = $this->getNewStatement($create);
        try {
            $statement->execute();
        } catch (\PHPUnit_Framework_Error_Warning $ex) {
            if ($ex->getMessage() !== 'oci_execute(): ORA-00942: table or view does not exist') {
                throw $ex;
            }
        }

        $create = 'CREATE TABLE bind_example(name VARCHAR(20))';
        /** @var Statement $statement */
        $statement = $this->getNewStatement($create);
        $statement->execute();

        $create_pkg = '
CREATE OR REPLACE PACKAGE ARRAYBINDPKG1 AS
  TYPE ARRTYPE IS TABLE OF VARCHAR(20) INDEX BY BINARY_INTEGER;
  PROCEDURE iobind(c1 IN OUT ARRTYPE);
END ARRAYBINDPKG1;';
        $statement = $this->getNewStatement($create_pkg);
        $statement->execute();

        $create_pkg_body = '
CREATE OR REPLACE PACKAGE BODY ARRAYBINDPKG1 AS
  CURSOR CUR IS SELECT name FROM bind_example;
  PROCEDURE iobind(c1 IN OUT ARRTYPE) IS
    BEGIN
    -- Bulk Insert
    FORALL i IN INDICES OF c1
      INSERT INTO bind_example VALUES (c1(i));

    -- Fetch and reverse
    IF NOT CUR%ISOPEN THEN
      OPEN CUR;
    END IF;
    FOR i IN REVERSE 1..5 LOOP
      FETCH CUR INTO c1(i);
      IF CUR%NOTFOUND THEN
        CLOSE CUR;
        EXIT;
      END IF;
    END LOOP;
  END iobind;
END ARRAYBINDPKG1;';
        $statement = $this->getNewStatement($create_pkg_body);
        $statement->execute();

        $sql = 'BEGIN arraybindpkg1.iobind(:c1); END;';
        $statement = $this->getNewStatement($sql);
        $array = array('one', 'two', 'three', 'four', 'five');
        $isSuccess = $statement->bindArrayByName(':c1', $array, 5, -1, SQLT_CHR);
        $this->assertTrue($isSuccess);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     * @expectedExceptionMessage oci_bind_array_by_name(): ORA-01036: illegal variable name/number
     * @expectedExceptionCode 1036
     */
    public function testCannotBindArrayByName()
    {
        $statement = $this->getNewStatement();
        $array = array('one', 'two', 'three', 'four', 'five');
        $statement->bindArrayByName(':c1', $array, 5, -1, SQLT_CHR);
    }

    public function testCanBindByName()
    {
        $statement = $this->getNewStatement('SELECT DUMMY FROM SYS.DUAL WHERE DUMMY LIKE :dummy');
        $value = 'X';
        $isBound = $statement->bindByName(':dummy', $value);
        $this->assertTrue($isBound);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     * @expectedExceptionMessage oci_bind_by_name(): ORA-01036: illegal variable name/number
     * @expectedExceptionCode 1036
     */
    public function testCannotBindByName()
    {
        $statement = $this->getNewStatement();
        $value = 'X';
        $statement->bindByName(':dummy', $value);
    }

    public function testCanStopReadingFromCursor()
    {
        $statement = $this->getNewStatement();
        $isSuccess = $statement->cancel();
        $this->assertTrue($isSuccess);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     */
    public function testFailStopReadingFromCursor()
    {
        $statement = $this->getClosedStatement();
        $isSuccess = $statement->cancel();
        $this->assertTrue($isSuccess);
    }

    public function testCanBindVariableByName()
    {
        $statement = $this->getNewStatement();
        $isSuccess = $statement->defineByName('DUMMY', $dummy);
        $this->assertTrue($isSuccess);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     */
    public function testCannotBindVariableByName()
    {
        $statement = $this->getClosedStatement();
        $isSuccess = $statement->defineByName('NOT_FOUND', $dummy);
        $this->assertTrue($isSuccess);
    }

    public function testGetError()
    {
        $statement = $this->getNewStatement();
        $value = 'X';
        try {
            $statement->bindByName(':dummy', $value);
        } catch (Oci8Exception $ex){
            //
        }
        $error = $statement->getError();
        $this->assertTrue(is_array($error));

        $this->assertArrayHasKey('code', $error);
        $this->assertArrayHasKey('message', $error);
        $this->assertArrayHasKey('offset', $error);
        $this->assertArrayHasKey('sqltext', $error);
    }

    public function testGetEmptyError()
    {
        $statement = $this->getNewStatement();
        $error = $statement->getError();
        $this->assertFalse($error);
    }

    public function testCanExecute()
    {
        $statement = $this->getNewStatement();
        $isSuccess = $statement->execute();
        $this->assertTrue($isSuccess);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     */
    public function testCannotExecute()
    {
        $statement = $this->getClosedStatement();
        $statement->execute();
    }

    public function testCanFetchAll()
    {
        $statement = $this->getNewStatement();
        $statement->execute();
        $statement->fetchAll($rows);
        $this->assertTrue(is_array($rows));
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     */
    public function testCannotFetchAll()
    {
        $statement = $this->getNewStatement();
        $statement->execute();
        $statement->free();
        $statement->fetchAll($rows);
    }

    // DESDE AQUI
    public function testCanFetchArrayBoth()
    {
        $statement = $this->getNewStatement();
        $statement->execute();
        $results = $statement->fetchArray();
        $this->assertTrue(is_array($results));
        $this->assertTrue(array_key_exists(0, $results));
        $this->assertTrue(array_key_exists('DUMMY', $results));
        $this->assertSame($results['DUMMY'], $results[0]);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     */
    public function testCannotFetchArrayBoth()
    {
        $statement = $this->getNewStatement();
        $statement->execute();
        $statement->free();
        $statement->fetchArray();
    }

    public function testCanFetchArrayNumeric()
    {
        $statement = $this->getNewStatement();
        $statement->execute();
        $row = $statement->fetchArray(OCI_NUM);
        $this->assertTrue(is_array($row));
        $this->assertTrue(array_key_exists(0, $row));
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     */
    public function testCannotFetchArrayNumeric()
    {
        $statement = $this->getNewStatement();
        $statement->execute();
        $statement->free();
        $statement->fetchArray(OCI_NUM);
    }

    public function testCanFetchArrayAssoc()
    {
        $statement = $this->getNewStatement();
        $statement->execute();
        $row = $statement->fetchArray(OCI_ASSOC);
        $this->assertTrue(is_array($row));
        $this->assertTrue(array_key_exists('DUMMY', $row));
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     */
    public function testCannotFetchArrayAssoc()
    {
        $statement = $this->getNewStatement();
        $statement->execute();
        $statement->free();
        $statement->fetchArray(OCI_ASSOC);
    }

    public function testCanFetchArrayAssocWithNulls()
    {
        $statement = $this->getNewStatement();
        $statement->execute();
        $row = $statement->fetchArray(OCI_ASSOC|OCI_RETURN_NULLS);
        $this->assertTrue(is_array($row));
        $this->assertTrue(array_key_exists('DUMMY', $row));
    }

    public function testCanFetchArrayAssocWithLobs()
    {
        $statement = $this->getNewStatement();
        $statement->execute();
        $row = $statement->fetchArray(OCI_ASSOC|OCI_RETURN_LOBS);
        $this->assertTrue(is_array($row));
        $this->assertTrue(array_key_exists('DUMMY', $row));
    }

    public function testCanFetchArrayAssocWithLobsAndNulls()
    {
        $statement = $this->getNewStatement();
        $statement->execute();
        $row = $statement->fetchArray(OCI_ASSOC|OCI_RETURN_LOBS|OCI_RETURN_NULLS);
        $this->assertTrue(is_array($row));
        $this->assertTrue(array_key_exists('DUMMY', $row));
    }

    public function testCanFetchAssoc()
    {
        $statement = $this->getNewStatement();
        $statement->execute();
        $row = $statement->fetchAssoc();
        $this->assertTrue(is_array($row));
        $this->assertTrue(array_key_exists('DUMMY', $row));
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     */
    public function testCannotFetchAssoc()
    {
        $statement = $this->getNewStatement();
        $statement->execute();
        $statement->free();
        $statement->fetchAssoc();
    }

    public function testCanFetchObject()
    {
        $statement = $this->getNewStatement();
        $statement->execute();
        $row = $statement->fetchObject();
        $this->assertTrue(is_object($row));
        $this->assertInstanceOf('\stdClass', $row);
        $this->assertTrue(property_exists($row, 'DUMMY'));
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     */
    public function testCannotFetchObject()
    {
        $statement = $this->getNewStatement();
        $statement->execute();
        $statement->free();
        $statement->fetchObject();
    }

    public function testCanFetchNumeric()
    {
        $statement = $this->getNewStatement();
        $statement->execute();
        $row = $statement->fetchRow();
        $this->assertTrue(is_array($row));
        $this->assertTrue(array_key_exists(0, $row));
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     */
    public function testCannotFetchNumeric()
    {
        $statement = $this->getNewStatement();
        $statement->execute();
        $statement->free();
        $statement->fetchRow();
    }

    public function testCanFetch()
    {
        $statement = $this->getNewStatement("SELECT 'X' AS DUMMY FROM SYS.DUAL");
        $statement->defineByName('DUMMY', $dummy);
        $statement->execute();
        $isFetched = $statement->fetch();
        $this->assertTrue($isFetched);
        $this->assertSame('X', $dummy);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     */
    public function testCannotFetch()
    {
        $statement = $this->getNewStatement("SELECT 'X' AS DUMMY FROM SYS.DUAL");
        $statement->defineByName('DUMMY', $dummy);
        $statement->execute();
        $statement->free();
        $isFetched = $statement->fetch();
    }

    public function testGetField()
    {
        $statement = $this->getNewStatement("SELECT DUMMY FROM SYS.DUAL");
        $statement->execute();
        $statement->fetchAssoc();

        $field1 = $statement->getField('DUMMY');
        $this->assertInstanceOf('\Jpina\Oci8\Oci8FieldInterface', $field1);

        $field2 = $statement->getField(1);
        $this->assertInstanceOf('\Jpina\Oci8\Oci8FieldInterface', $field2);
    }


    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     */
    public function testCannotGetField()
    {
        $statement = $this->getNewStatement("SELECT DUMMY FROM SYS.DUAL");
        $statement->execute();
        $statement->fetchAssoc();
        $statement->free();
        $statement->getField('DUMMY');
    }

    public function testGetImplicitResultset()
    {
        $database = self::$database;
        if ($database->getClientMayorVersion() < 12 || $database->getServerMayorVersion() < 12) {
            $this->markTestSkipped("Implicit Result Sets (IRS) is available starting on Oracle Database 12c");
        }

        $sql =
            "declare
                c1 sys_refcursor;
            begin
                open c1 for select * from sys.dual;
                dbms_sql.return_result(c1);
            end;";
        $statement = $this->getNewStatement($sql);
        $statement->execute();
        $childStatement = $statement->getImplicitResultset();

        $this->assertInstanceOf('\Jpina\Oci8\Oci8StatementInterface', $childStatement);
    }

    public function testGetImplicitResultsetError()
    {
        $statement = $this->getNewStatement();
        $childStatement = $statement->getImplicitResultset();
        $this->assertFalse($childStatement);
    }

    public function testGetNumFields()
    {
        $statement = $this->getNewStatement();
        $statement->execute();
        $statement->fetch();
        $this->assertGreaterThanOrEqual(0, $statement->getNumFields());
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     */
    public function testCannotGetNumFields()
    {
        $statement = $this->getClosedStatement();
        $statement->getNumFields();
    }

    public function testGetNumRows()
    {
        $statement = $this->getNewStatement();
        $statement->execute();
        $statement->fetch();
        $this->assertGreaterThanOrEqual(0, $statement->getNumRows());
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     */
    public function testCannotGetNumRows()
    {
        $statement = $this->getClosedStatement();
        $this->assertGreaterThanOrEqual(0, $statement->getNumRows());
    }

    public function testSetPrefetch()
    {
        $statement = $this->getNewStatement();
        $isSuccess = $statement->setPrefetch(10);
        $this->assertTrue($isSuccess);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     */
    public function testCannotSetPrefetch()
    {
        $statement = $this->getClosedStatement();
        $statement->setPrefetch(10);
    }

    public function testGetStatementType()
    {
        $statement = $this->getNewStatement();
        $type = $statement->getType();
        $this->assertEquals('SELECT', $type);
    }

    /**
     * @expectedException \Jpina\Oci8\Oci8Exception
     */
    public function testCannotGetStatementType()
    {
        $statement = $this->getClosedStatement();
        $statement->getType();
    }

    public function testFreeStatement()
    {
        $statement = $this->getNewStatement();
        $isSuccess = $statement->free();
        $this->assertTrue($isSuccess);
    }

    public function testCannotFreeStatement()
    {
        $statement = $this->getClosedStatement();
        $isSuccess = $statement->free();
        $this->assertFalse($isSuccess);
    }
}
