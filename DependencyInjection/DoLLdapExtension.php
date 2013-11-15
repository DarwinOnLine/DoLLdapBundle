<?php

namespace DoL\LdapBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 *
 * @author DarwinOnLine
 * @author Maks3w
 * @link https://github.com/DarwinOnLine/DoLLdapBundle
 */
class DoLLdapExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        foreach (array('services', 'security', 'validator', 'ldap_driver') as $basename) {
            $loader->load(sprintf('%s.yml', $basename));
        }

        $container->setAlias('dol_ldap.user_manager', $config['service']['user_manager']);
        $container->setAlias('dol_ldap.ldap_manager', $config['service']['ldap_manager']);
        $container->setAlias('dol_ldap.ldap_driver', $config['service']['ldap_driver']);

        foreach ($config['domains'] as &$domain)
        {
            if (!isset($domain['driver']['baseDn'])) {
                $domain['driver']['baseDn'] = $domain['user']['baseDn'];
            }
            if (!isset($domain['driver']['accountFilterFormat'])) {
                $domain['driver']['accountFilterFormat'] = $domain['user']['filter'];
            }
        }
        
        $container->setParameter('dol_ldap.domains.parameters', $config['domains']);
    }
    
    public function getNamespace()
    {
        return 'dol_ldap';
    }
}
