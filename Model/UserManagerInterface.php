<?php

namespace DoL\LdapBundle\Model;

use DoL\LdapBundle\Model\LdapUserInterface;

/**
 * User manager interface.
 * Ldap user managers must implement this interface.
 *
 * @author DarwinOnLine
 * @author Maks3w
 * @link https://github.com/DarwinOnLine/DoLLdapBundle
 */
interface UserManagerInterface
{
    /**
     * Creates an empty user instance.
     *
     * @return LdapUserInterface
     */
    public function createUser();

    /**
     * Find a user by his username.
     *
     * @param string $username
     *
     * @return LdapUserInterface|null
     */
    public function findUserByUsername($username);
}
