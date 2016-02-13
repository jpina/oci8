<?php

namespace Jpina\Oci8;

/**
 * Class Connection
 * @package Jpina\Oci8
 * @see http://php.net/manual/en/book.oci8.php
 */
class Oci8Connection extends Oci8Base implements Oci8ConnectionInterface
{
    /**
     * Connection constructor.
     * @param $username
     * @param $password
     * @param null $connectionString
     * @param null $characterSet
     * @param null $sessionMode
     * @throws \Jpina\Oci8\Oci8Exception
     */
    public function __construct(
        $username,
        $password,
        $connectionString = null,
        $characterSet = null,
        $sessionMode = null
    ) {
        $this->resource = $this->connect($username, $password, $connectionString, $characterSet, $sessionMode);
    }

    public function changePassword($username, $oldPassword, $newPassword)
    {
        set_error_handler($this->getErrorHandler());
        $isSuccess = oci_password_change($this->resource, $username, $oldPassword, $newPassword);
        restore_error_handler();
        return $isSuccess;
    }

    public function close()
    {
        $isSuccess = false;
        if ($this->resource) {
            set_error_handler($this->getErrorHandler());
            $isSuccess = oci_close($this->resource);
            restore_error_handler();
        }

        if ($isSuccess) {
            $this->resource = null;
        }

        return $isSuccess;
    }

    public function commit()
    {
        set_error_handler($this->getErrorHandler());
        $isSuccess = oci_commit($this->resource);
        restore_error_handler();
        return $isSuccess;
    }

    /**
     * Connect to the Oracle server using a unique connection
     *
     * @param string $username
     * @param string $password
     * @param string $connectionString
     * @param string $characterSet
     * @param int $sessionMode
     * @return resource
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-new-connect.php
     */
    protected function connect(
        $username,
        $password,
        $connectionString = null,
        $characterSet = null,
        $sessionMode = null
    ) {
        set_error_handler($this->getErrorHandler());
        $connection = oci_new_connect($username, $password, $connectionString, $characterSet, $sessionMode);
        restore_error_handler();
        return $connection;
    }

    public function copyLob($lobTo, $lobFrom, $length = 0)
    {
        set_error_handler($this->getErrorHandler());
        $isSuccess = oci_lob_copy($lobTo, $lobFrom, $length);
        restore_error_handler();
        return $isSuccess;
    }

    public function freeDescriptor($descriptor)
    {
        set_error_handler($this->getErrorHandler());
        $isSuccess = oci_free_descriptor($descriptor);
        restore_error_handler();
        return $isSuccess;
    }

    public function getClientMayorVersion()
    {
        $clientVersion = $this->getClientVersion();
        return (int)substr($clientVersion, 0, strpos($clientVersion, '.'));
    }

    public function getClientVersion()
    {
        return oci_client_version();
    }

    public function getNewCollection($tdo, $schema = null)
    {
        set_error_handler($this->getErrorHandler());
        $collection = oci_new_collection($this->resource, $tdo, $schema);
        restore_error_handler();
        return $collection;
    }

    public function getNewCursor()
    {
        set_error_handler($this->getErrorHandler());
        $cursor = oci_new_cursor($this->resource);
        restore_error_handler();
        return new Oci8Cursor($cursor);
    }

    public function getNewDescriptor($type = OCI_DTYPE_LOB)
    {
        set_error_handler($this->getErrorHandler());
        $descriptor = oci_new_descriptor($this->resource, $type);
        restore_error_handler();
        return $descriptor;
    }

    public function getServerMayorVersion()
    {
        preg_match('/\\d+(:?\\.\\d+)+/', $this->getServerVersion(), $matches);
        return (int)substr($matches[0], 0, strpos($matches[0], '.'));
    }

    public function getServerVersion()
    {
        set_error_handler($this->getErrorHandler());
        $serverVersion = oci_server_version($this->resource);
        restore_error_handler();
        return $serverVersion;
    }

    public function isLobEqual($lob1, $lob2)
    {
        set_error_handler($this->getErrorHandler());
        $isEquals = oci_lob_is_equal($lob1, $lob2);
        restore_error_handler();
        return $isEquals;
    }

    public function parse($sqlText)
    {
        set_error_handler($this->getErrorHandler());
        $resource = oci_parse($this->resource, $sqlText);
        restore_error_handler();
        return new Oci8Statement($resource);
    }

    public function setAction($actionName)
    {
        set_error_handler($this->getErrorHandler());
        $isSuccess = oci_set_action($this->resource, $actionName);
        restore_error_handler();
        return $isSuccess;
    }

    public function setClientIdentifier($clientIdentifier)
    {
        set_error_handler($this->getErrorHandler());
        $isSuccess = oci_set_client_identifier($this->resource, $clientIdentifier);
        restore_error_handler();
        return $isSuccess;
    }

    public function setClientInfo($clientInfo)
    {
        set_error_handler($this->getErrorHandler());
        $isSuccess = oci_set_client_info($this->resource, $clientInfo);
        restore_error_handler();
        return $isSuccess;
    }

    public function setEdition($edition)
    {
        set_error_handler($this->getErrorHandler());
        $isSuccess = oci_set_edition($edition);
        restore_error_handler();
        return $isSuccess;
    }

    public function setInternalDebug($onOff)
    {
        oci_internal_debug($onOff);
    }

    public function setModuleName($moduleName)
    {
        set_error_handler($this->getErrorHandler());
        $isSuccess = oci_set_module_name($this->resource, $moduleName);
        restore_error_handler();
        return $isSuccess;
    }

    public function rollback()
    {
        set_error_handler($this->getErrorHandler());
        $isSuccess = oci_rollback($this->resource);
        restore_error_handler();
        return $isSuccess;
    }
}
