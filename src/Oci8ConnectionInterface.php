<?php

namespace Jpina\Oci8;

/**
 * Interface Oci8ConnectionInterface
 * @package Jpina\Oci8
 * @see http://php.net/manual/en/book.oci8.php
 */
interface Oci8ConnectionInterface
{

    /**
     * Changes password of Oracle's user
     *
     * @param string $username
     * @param string $oldPassword
     * @param string $newPassword
     * @return bool
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-password-change.php
     */
    public function changePassword($username, $oldPassword, $newPassword);

    /**
     * Closes an Oracle connection
     *
     * @return bool
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-close.php
     */
    public function close();

    /**
     * Commits the outstanding database transaction
     *
     * @return bool
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-commit.php
     */
    public function commit();

    /**
     * Copies large object
     *
     * @param \OCI-Lob $lobTo
     * @param \OCI-Lob $lobFrom
     * @param int $length
     * @return bool
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-lob-copy.php
     */
    public function copyLob($lobTo, $lobFrom, $length = 0);

    /**
     * Frees a descriptor
     *
     * @param resource $descriptor
     * @return bool
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-free-descriptor.php
     */
    public function freeDescriptor($descriptor);

    /**
     * Returns the Oracle client library mayor version
     *
     * @return int
     */
    public function getClientMayorVersion();

    /**
     * Returns the Oracle client library version
     *
     * @return string
     * @see http://php.net/manual/en/function.oci-client-version.php
     */
    public function getClientVersion();

    /**
     * Returns the last error found
     *
     * @return array|bool
     * @see http://php.net/manual/en/function.oci-error.php
     */
    public function getError();

    /**
     * Allocates new collection object
     *
     * @param string $tdo
     * @param string $schema
     * @return \OCI_Collection
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-new-collection.php
     */
    public function getNewCollection($tdo, $schema = null);

    /**
     * Allocates and returns a new cursor (statement handle)
     *
     * @return \Jpina\Oci8\Oci8CursorInterface
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-new-cursor.php
     */
    public function getNewCursor();

    /**
     * Initializes a new empty LOB or FILE descriptor
     *
     * @param int $type
     * @return \OCI_Lob
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-new-descriptor.php
     */
    public function getNewDescriptor($type = OCI_DTYPE_LOB);

    /**
     * Returns the Oracle client library mayor version
     *
     * @return int
     * @throws \Jpina\Oci8\Oci8Exception
     */
    public function getServerMayorVersion();

    /**
     * Returns the Oracle Database version
     *
     * @return string
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-server-version.php
     */
    public function getServerVersion();

    /**
     * Compares two LOB/FILE locators for equality
     *
     * @param \OCI-Lob $lob1
     * @param \OCI-Lob $lob2
     * @return bool
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-lob-is-equal.php
     */
    public function isLobEqual($lob1, $lob2);

    /**
     * Prepares an Oracle statement for execution
     *
     * @param string $sqlText
     * @return \Jpina\Oci8\Oci8StatementInterface
     * @throws \Jpina\Oci8\Oci8Exception
     */
    public function parse($sqlText);

    /**
     * Sets the action name
     *
     * @param string $actionName
     * @return bool
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-set-action.php
     */
    public function setAction($actionName);

    /**
     * Sets the client identifier
     *
     * @param string $clientIdentifier
     * @return bool
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-set-client-identifier.php
     */
    public function setClientIdentifier($clientIdentifier);

    /**
     * Sets the client information
     *
     * @param string $clientInfo
     * @return bool
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-set-client-info.php
     */
    public function setClientInfo($clientInfo);

    /**
     * Sets the database edition
     *
     * @param string $edition
     * @return bool
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-set-edition.php
     */
    public static function setEdition($edition);

    /**
     * Enables or disables internal debug output
     *
     * @param bool $onOff
     * @see http://php.net/manual/en/function.oci-internal-debug.php
     */
    public function setInternalDebug($onOff);

    /**
     * Sets the module name
     *
     * @param string $moduleName
     * @return bool
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-set-module-name.php
     */
    public function setModuleName($moduleName);

    /**
     * Rolls back the outstanding database transaction
     *
     * @return bool
     * @throws \Jpina\Oci8\Oci8Exception
     * @see http://php.net/manual/en/function.oci-rollback.php
     */
    public function rollback();
}
