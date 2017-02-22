<?php

namespace DoL\LdapBundle\Driver;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Driver interface.
 * Ldap drivers must implement this interface.
 *
 * @see http://www.php.net/ref.ldap.php
 *
 * @author DarwinOnLine
 * @author Maks3w
 *
 * @see https://github.com/DarwinOnLine/DoLLdapBundle
 */
interface LdapDriverInterface
{
    /**
     * Initialize the driver (new options).
     *
     * @param array $options new options to connect
     */
    public function init(array $options);

    /**
     * Bind to LDAP directory.
     *
     * @param UserInterface $user     the user for authenticating the bind
     * @param string        $password the password for authenticating the bind
     *
     * @return bool true on success or false on failure
     *
     * @throws LdapDriverException if some error occurs
     */
    public function bind(UserInterface $user, $password);

    /**
     * Search LDAP tree.
     *
     * @param string $baseDn     the base DN for the directory
     * @param string $filter     the search filter
     * @param array  $attributes The array of the required attributes,
     *                           'dn' is always returned. If array is
     *                           empty then will return all attributes
     *                           and their associated values.
     *
     * @return array|bool Returns a complete result information in a
     *                    multi-dimensional array on success and FALSE on error.
     *                    see {@link http://www.php.net/function.ldap-get-entries.php}
     *                    for array format examples.
     *
     * @throws LdapDriverException if some error occurs
     */
    public function search($baseDn, $filter, array $attributes = array());
}
