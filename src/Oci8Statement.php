<?php

namespace Jpina\Oci8;

/**
 * Class Oci8Statement
 * @package Jpina\Oci8
 * @see http://php.net/manual/en/book.oci8.php
 */
class Oci8Statement extends AbstractOci8Base implements Oci8StatementInterface
{
    /** @var resource */
    protected $resource;

    /**
     * Statement constructor
     * @param resource $resource
     * @throws \Jpina\Oci8\Oci8Exception
     */
    public function __construct($resource)
    {
        if (!is_resource($resource) || get_resource_type($resource) !== 'oci8 statement') {
            throw new Oci8Exception('resource is not an oci8 statement', 0, E_ERROR, __FILE__, __LINE__);
        }

        $this->resource = $resource;
    }

    public function bindArrayByName($name, &$varArray, $maxTableLength, $maxItemLength = -1, $type = SQLT_AFC)
    {
        set_error_handler(static::getErrorHandler());
        $isSuccess = oci_bind_array_by_name($this->resource, $name, $varArray, $maxTableLength, $maxItemLength, $type);
        restore_error_handler();
        return $isSuccess;
    }

    public function bindByName($bvName, &$variable, $maxLength = -1, $type = SQLT_CHR)
    {
        set_error_handler(static::getErrorHandler());
        $isSuccess = oci_bind_by_name($this->resource, $bvName, $variable, $maxLength, $type);
        restore_error_handler();
        return $isSuccess;
    }

    public function cancel()
    {
        set_error_handler(static::getErrorHandler());
        $isSuccess = oci_cancel($this->resource);
        restore_error_handler();
        return $isSuccess;
    }

    public function defineByName($columnName, &$variable, $type = SQLT_CHR)
    {
        set_error_handler(static::getErrorHandler());
        $isSuccess = oci_define_by_name($this->resource, $columnName, $variable, $type);
        restore_error_handler();
        return $isSuccess;
    }

    public function execute($mode = OCI_COMMIT_ON_SUCCESS)
    {
        set_error_handler(static::getErrorHandler());
        $isSuccess = oci_execute($this->resource, $mode);
        restore_error_handler();
        return $isSuccess;
    }

    public function fetchAll(&$output, $skip = 0, $maxRows = -1, $flags = 0)
    {
        if (empty($flags)) {
            $flags = OCI_FETCHSTATEMENT_BY_COLUMN | OCI_ASSOC;
        }

        set_error_handler(static::getErrorHandler());
        $numRows = oci_fetch_all($this->resource, $output, $skip, $maxRows, $flags);
        restore_error_handler();
        return $numRows;
    }

    public function fetchArray($mode = OCI_BOTH)
    {
        set_error_handler(static::getErrorHandler());
        $row = oci_fetch_array($this->resource, $mode);
        restore_error_handler();
        return $row;
    }

    public function fetchAssoc()
    {
        set_error_handler(static::getErrorHandler());
        $row = oci_fetch_assoc($this->resource);
        restore_error_handler();
        return $row;
    }

    public function fetchObject()
    {
        set_error_handler(static::getErrorHandler());
        $row = oci_fetch_object($this->resource);
        restore_error_handler();
        return $row;
    }

    public function fetchRow()
    {
        set_error_handler(static::getErrorHandler());
        $row = oci_fetch_row($this->resource);
        restore_error_handler();
        return $row;
    }

    public function fetch()
    {
        set_error_handler(static::getErrorHandler());
        $isSuccess = oci_fetch($this->resource);
        restore_error_handler();
        return $isSuccess;
    }

    public function getField($field)
    {
        set_error_handler(static::getErrorHandler());
        $name = oci_field_name($this->resource, $field);
        $precision = oci_field_precision($this->resource, $field);
        $scale = oci_field_scale($this->resource, $field);
        $size = oci_field_size($this->resource, $field);
        $rawType = oci_field_type_raw($this->resource, $field);
        $type = oci_field_type($this->resource, $field);
        $value = oci_field_is_null($this->resource, $field) ? null : oci_result($this->resource, $field);
        $field = new Oci8Field($name, $value, $size, $precision, $scale, $type, $rawType);
        restore_error_handler();

        return $field;
    }

    public function free()
    {
        $isSuccess = false;
        set_error_handler(static::getErrorHandler());
        if ($this->resource) {
            $isSuccess = oci_free_statement($this->resource);
        }
        restore_error_handler();

        if ($isSuccess) {
            $this->resource = null;
        }
        return $isSuccess;
    }

    public function getImplicitResultset()
    {
        $resultset = false;
        set_error_handler(static::getErrorHandler());
        $resource = oci_get_implicit_resultset($this->resource);
        if ($resource) {
            $resultset = new static($resource);
        }
        restore_error_handler();
        return $resultset;
    }

    public function getNumFields()
    {
        set_error_handler(static::getErrorHandler());
        $numFields = oci_num_fields($this->resource);
        restore_error_handler();
        return $numFields;
    }

    public function getNumRows()
    {
        set_error_handler(static::getErrorHandler());
        $numRows = oci_num_rows($this->resource);
        restore_error_handler();
        return $numRows;
    }

    public function setPrefetch($rows)
    {
        set_error_handler(static::getErrorHandler());
        $isSuccess = oci_set_prefetch($this->resource, $rows);
        restore_error_handler();
        return $isSuccess;
    }

    public function getType()
    {
        set_error_handler(static::getErrorHandler());
        $type = oci_statement_type($this->resource);
        restore_error_handler();
        return $type;
    }
}
