<?php

namespace DoL\LdapBundle\Tests\DependencyInjection;

/**
 * Provides default configuration options for the bundle and each section.
 *
 * @author DarwinOnLine
 * @author Maks3w
 *
 * @see https://github.com/DarwinOnLine/DoLLdapBundle
 */
trait ConfigurationTrait
{
    /**
     * Returns default configuration bundle configuration.
     *
     * @return array
     */
    protected function getDefaultConfig()
    {
        return [
            'domains' => [
                'server1' => [
                    'driver' => $this->getDefaultDriverConfig(),
                    'user' => $this->getDefaultUserConfig(),
                ],
            ],
            'service' => $this->getDefaultServiceConfig(),
        ];
    }

    /**
     * Returns default configuration for Driver subtree.
     *
     * Same as service parameter `dol_ldap.ldap_driver.parameters`
     *
     * @return array
     */
    protected function getDefaultDriverConfig()
    {
        return [
            'host' => 'ldap.hostname.local',
            'port' => 389,
            'useSsl' => false,
            'useStartTls' => false,
            'baseDn' => 'ou=Persons,dc=example,dc=com',
            'accountFilterFormat' => '',
            'bindRequiresDn' => false,
        ];
    }

    /**
     * Returns default configuration for User subtree.
     *
     * Same as service parameter `dol_ldap.ldap_manager.parameters`
     *
     * @return array
     */
    protected function getDefaultUserConfig()
    {
        return [
            'baseDn' => 'ou=Persons,dc=example,dc=com',
            'filter' => '',
            'usernameAttribute' => 'uid',
            'attributes' => [
                [
                    'ldap_attr' => 'uid',
                    'user_method' => 'setUsername',
                ],
            ],
        ];
    }

    /**
     * Returns default configuration for Service subtree.
     *
     * @return array
     */
    protected function getDefaultServiceConfig()
    {
        return [
            'user_hydrator' => 'dol_ldap.user_hydrator.default',
            'ldap_manager' => 'dol_ldap.ldap_manager.default',
            'ldap_driver' => 'dol_ldap.ldap_driver.zend',
        ];
    }
}
