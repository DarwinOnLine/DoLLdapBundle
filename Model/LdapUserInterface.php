<?php

namespace DoL\LdapBundle\Model;

/**
 * User interface.
 * Ldap users must implement this interface.
 *
 * @author DarwinOnLine
 * @author Maks3w
 * @link https://github.com/DarwinOnLine/DoLLdapBundle
 */
interface LdapUserInterface
{
    /**
     * Set Ldap Distinguished Name
     *
     * @param string $dn Distinguished Name
     */
    public function setDn($dn);

    /**
     * Get Ldap Distinguished Name
     *
     * @return string Distinguished Name
     */
    public function getDn();
}
