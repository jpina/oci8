<?php

namespace Jpina\Oci8\Test;

use Jpina\Oci8\Oci8Connection;

class Oci8FieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Jpina\Oci8\Oci8StatementInterface
     */
    protected static $statement;

    public static function setUpBeforeClass()
    {
        $database = new Oci8Connection(
            getenv('DB_USER'),
            getenv('DB_PASSWORD'),
            '//' . getenv('DB_HOST') . ':' . getenv('DB_PORT') . '/' . getenv('DB_SCHEMA'),
            getenv('DB_CHARSET'),
            OCI_DEFAULT
        );

        $sql = "SELECT " .
            "NULL AS DUMMY, " .
            "'X' AS D_VARCHAR, " .
            "CAST(1 AS INT) AS D_INT, " .
            "CAST(9.9 AS FLOAT) AS D_FLOAT, " .
            "CAST(98.33 AS NUMBER(5,2)) AS D_NUMBER " .
            "FROM SYS.DUAL";

        /** @var \Jpina\Oci8\Oci8StatementInterface $statemet */
        $statemet = $database->parse($sql);
        $statemet->execute();
        $statemet->fetchAssoc();

        static::$statement = $statemet;
    }

    /**
     * @return \Jpina\Oci8\Oci8FieldInterface
     */
    protected function getField($field)
    {
        return static::$statement->getField($field);
    }

    public function testFieldNameByName()
    {
        $field = $this->getField('DUMMY');
        $this->assertEquals('DUMMY', $field->getName());
        $field = $this->getField('D_VARCHAR');
        $this->assertEquals('D_VARCHAR', $field->getName());
        $field = $this->getField('D_INT');
        $this->assertEquals('D_INT', $field->getName());
        $field = $this->getField('D_FLOAT');
        $this->assertEquals('D_FLOAT', $field->getName());
        $field = $this->getField('D_NUMBER');
        $this->assertEquals('D_NUMBER', $field->getName());
    }

    public function testFieldNameByIndex()
    {
        $field = $this->getField(1);
        $this->assertEquals('DUMMY', $field->getName());
        $field = $this->getField(2);
        $this->assertEquals('D_VARCHAR', $field->getName());
        $field = $this->getField(3);
        $this->assertEquals('D_INT', $field->getName());
        $field = $this->getField(4);
        $this->assertEquals('D_FLOAT', $field->getName());
        $field = $this->getField(5);
        $this->assertEquals('D_NUMBER', $field->getName());
    }

    public function testFieldValueByName()
    {
        $field = $this->getField('DUMMY');
        $this->assertNull($field->getValue());
        $field = $this->getField('D_VARCHAR');
        $this->assertEquals('X', $field->getValue());
        $field = $this->getField('D_INT');
        $this->assertEquals(1, $field->getValue());
        $field = $this->getField('D_FLOAT');
        $this->assertEquals(9.9, $field->getValue());
        $field = $this->getField('D_NUMBER');
        $this->assertEquals(98.33, $field->getValue());
    }

    public function testFieldValueByIndex()
    {
        $field = $this->getField(1);
        $this->assertNull($field->getValue());
        $field = $this->getField(2);
        $this->assertEquals('X', $field->getValue());
        $field = $this->getField(3);
        $this->assertEquals(1, $field->getValue());
        $field = $this->getField(4);
        $this->assertEquals(9.9, $field->getValue());
        $field = $this->getField(5);
        $this->assertEquals(98.33, $field->getValue());
    }

    public function testFieldPrecisionByName()
    {
        $field = $this->getField('DUMMY');
        $this->assertEquals(0, $field->getPrecision());
        $field = $this->getField('D_VARCHAR');
        $this->assertEquals(0, $field->getPrecision());
        $field = $this->getField('D_INT');
        $this->assertEquals(38, $field->getPrecision());
        $field = $this->getField('D_FLOAT');
        $this->assertEquals(126, $field->getPrecision());
        $field = $this->getField('D_NUMBER');
        $this->assertEquals(5, $field->getPrecision());
    }

    public function testFieldPrecisionByIndex()
    {
        $field = $this->getField(1);
        $this->assertEquals(0, $field->getPrecision());
        $field = $this->getField(2);
        $this->assertEquals(0, $field->getPrecision());
        $field = $this->getField(3);
        $this->assertEquals(38, $field->getPrecision());
        $field = $this->getField(4);
        $this->assertEquals(126, $field->getPrecision());
        $field = $this->getField(5);
        $this->assertEquals(5, $field->getPrecision());
    }

    public function testFieldScaleByName()
    {
        $field = $this->getField('DUMMY');
        $this->assertEquals(0, $field->getScale());
        $field = $this->getField('D_VARCHAR');
        $this->assertEquals(0, $field->getScale());
        $field = $this->getField('D_INT');
        $this->assertEquals(0, $field->getScale());
        $field = $this->getField('D_FLOAT');
        $this->assertEquals(-127, $field->getScale());
        $field = $this->getField('D_NUMBER');
        $this->assertEquals(2, $field->getScale());
    }

    public function testFieldScaleByIndex()
    {
        $field = $this->getField(1);
        $this->assertEquals(0, $field->getScale());
        $field = $this->getField(2);
        $this->assertEquals(0, $field->getScale());
        $field = $this->getField(3);
        $this->assertEquals(0, $field->getScale());
        $field = $this->getField(4);
        $this->assertEquals(-127, $field->getScale());
        $field = $this->getField(5);
        $this->assertEquals(2, $field->getScale());
    }

    public function testFieldSizeByName()
    {
        $field = $this->getField('DUMMY');
        $this->assertEquals(0, $field->getSize());
        $field = $this->getField('D_VARCHAR');
        $this->assertEquals(1, $field->getSize());
        $field = $this->getField('D_INT');
        $this->assertEquals(22, $field->getSize());
        $field = $this->getField('D_FLOAT');
        $this->assertEquals(22, $field->getSize());
        $field = $this->getField('D_NUMBER');
        $this->assertEquals(22, $field->getSize());
    }

    public function testFieldSizeByIndex()
    {
        $field = $this->getField(1);
        $this->assertEquals(0, $field->getSize());
        $field = $this->getField(2);
        $this->assertEquals(1, $field->getSize());
        $field = $this->getField(3);
        $this->assertEquals(22, $field->getSize());
        $field = $this->getField(4);
        $this->assertEquals(22, $field->getSize());
        $field = $this->getField(5);
        $this->assertEquals(22, $field->getSize());
    }

    public function testFieldRawTypeByName()
    {
        $field = $this->getField('DUMMY');
        $this->assertEquals(1, $field->getRawType());
        $field = $this->getField('D_VARCHAR');
        $this->assertEquals(96, $field->getRawType());
        $field = $this->getField('D_INT');
        $this->assertEquals(2, $field->getRawType());
        $field = $this->getField('D_FLOAT');
        $this->assertEquals(2, $field->getRawType());
        $field = $this->getField('D_NUMBER');
        $this->assertEquals(2, $field->getRawType());
    }

    public function testFieldRawTypeByIndex()
    {
        $field = $this->getField(1);
        $this->assertEquals(1, $field->getRawType());
        $field = $this->getField(2);
        $this->assertEquals(96, $field->getRawType());
        $field = $this->getField(3);
        $this->assertEquals(2, $field->getRawType());
        $field = $this->getField(4);
        $this->assertEquals(2, $field->getRawType());
        $field = $this->getField(5);
        $this->assertEquals(2, $field->getRawType());
    }

    public function testFieldTypeByName()
    {
        $field = $this->getField('DUMMY');
        $this->assertEquals('VARCHAR2', $field->getType());
        $field = $this->getField('D_VARCHAR');
        $this->assertEquals('CHAR', $field->getType());
        $field = $this->getField('D_INT');
        $this->assertEquals('NUMBER', $field->getType());
        $field = $this->getField('D_FLOAT');
        $this->assertEquals('NUMBER', $field->getType());
        $field = $this->getField('D_NUMBER');
        $this->assertEquals('NUMBER', $field->getType());
    }

    public function testFieldTypeByIndex()
    {
        $field = $this->getField(1);
        $this->assertEquals('VARCHAR2', $field->getType());
        $field = $this->getField(2);
        $this->assertEquals('CHAR', $field->getType());
        $field = $this->getField(3);
        $this->assertEquals('NUMBER', $field->getType());
        $field = $this->getField(4);
        $this->assertEquals('NUMBER', $field->getType());
        $field = $this->getField(5);
        $this->assertEquals('NUMBER', $field->getType());
    }

    public function testFieldIsNullByName()
    {
        $field = $this->getField('DUMMY');
        $this->assertTrue($field->isNull());
        $field = $this->getField('D_VARCHAR');
        $this->assertFalse($field->isNull());
        $field = $this->getField('D_INT');
        $this->assertFalse($field->isNull());
        $field = $this->getField('D_FLOAT');
        $this->assertFalse($field->isNull());
        $field = $this->getField('D_NUMBER');
        $this->assertFalse($field->isNull());
    }

    public function testFieldIsNullByIndex()
    {
        $field = $this->getField(1);
        $this->assertTrue($field->isNull());
        $field = $this->getField(2);
        $this->assertFalse($field->isNull());
        $field = $this->getField(3);
        $this->assertFalse($field->isNull());
        $field = $this->getField(4);
        $this->assertFalse($field->isNull());
        $field = $this->getField(5);
        $this->assertFalse($field->isNull());
    }
}
