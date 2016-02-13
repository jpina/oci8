<?php

namespace Jpina\Oci8;

interface Oci8StatementInterface
{

    /**
     * Binds a PHP array to an Oracle PL/SQL array parameter
     *
     * @param string $name
     * @param array $varArray
     * @param int $maxTableLength
     * @param int $maxItemLength
     * @param int $type
     * @return bool
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-bind-array-by-name.php
     */
    public function bindArrayByName($name, &$varArray, $maxTableLength, $maxItemLength = -1, $type = SQLT_AFC);

    /**
     * Binds a PHP variable to an Oracle placeholder
     *
     * @param string $bvName
     * @param mixed $variable
     * @param int $maxLength
     * @param int $type
     * @return bool
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-bind-by-name.php
     */
    public function bindByName($bvName, &$variable, $maxLength = -1, $type = SQLT_CHR);

    /**
     * Cancels reading from cursor
     *
     * @return bool
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-cancel.php
     */
    public function cancel();

    /**
     * Associates a PHP variable with a column for query fetches
     *
     * @param string $columnName
     * @param mixed $variable
     * @param int $type
     * @return bool
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-define-by-name.php
     */
    public function defineByName($columnName, &$variable, $type = SQLT_CHR);

    /**
     * Returns the last error found
     *
     * @return array
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-error.php
     */
    public function getError();

    /**
     * Executes a statement
     *
     * @param int $mode
     * @return bool
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-execute.php
     */
    public function execute($mode = OCI_COMMIT_ON_SUCCESS);

    /**
     * Fetches multiple rows from a query into a two-dimensional array
     *
     * @param array $output
     * @param int $skip
     * @param int $maxRows
     * @param int $flags
     * @return int
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-fetch-all.php
     */
    public function fetchAll(&$output, $skip = 0, $maxRows = -1, $flags = 0);

    /**
     * Returns the next row from a query as an associative or numeric array
     *
     * @param int $mode
     * @return array
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-fetch-array.php
     */
    public function fetchArray($mode = OCI_BOTH);

    /**
     * Returns the next row from a query as an associative array
     *
     * @return array
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-fetch-assoc.php
     */
    public function fetchAssoc();

    /**
     * Returns the next row from a query as an object
     *
     * @return object
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-fetch-object.php
     */
    public function fetchObject();

    /**
     * Returns the next row from a query as a numeric array
     *
     * @return array
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-fetch-row.php
     */
    public function fetchRow();

    /**
     * Fetches the next row from a query into internal buffers
     *
     * @return bool
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-fetch.php
     */
    public function fetch();

    /**
     * Returns an Oci8Field instance
     *
     * @param mixed $field
     * @return \Jpina\Oci8\Oci8FieldInterface
     * @throws \Jpina\Oci8\Oci8Exception
     */
    public function getField($field);

    /**
     * Frees all resources associated with statement or cursor
     *
     * @return bool
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-free-statement.php
     */
    public function free();

    /**
     * Returns the next child statement resource from a parent statement resource that has
     * Oracle Database 12c Implicit Result Sets
     *
     * @return \Jpina\Oci8\Oci8StatementInterface
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-get-implicit-resultset.php
     */
    public function getImplicitResultset();

    /**
     * Returns the number of result columns in a statement
     *
     * @return int
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-num-fields.php
     */
    public function getNumFields();

    /**
     * Returns number of rows affected during statement execution
     *
     * @return int
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-num-rows.php
     */
    public function getNumRows();

    /**
     * Sets number of rows to be prefetched by queries
     *
     * @param int $rows
     * @return bool
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-set-prefetch.php
     */
    public function setPrefetch($rows);

    /**
     * Returns the type of a statement
     *
     * @return string
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-statement-type.php
     */
    public function getType();
}
