<?php

namespace DoL\LdapBundle;

use DoL\LdapBundle\Security\Factory\FormLoginLdapFactory;
use DoL\LdapBundle\Security\Factory\HttpBasicLdapFactory;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * DoLLdapBundle.
 *
 * @author DarwinOnLine
 * @author Maks3w
 *
 * @see https://github.com/DarwinOnLine/DoLLdapBundle
 */
class DoLLdapBundle extends Bundle
{
    public function boot()
    {
        if (!function_exists('ldap_connect')) {
            throw new \Exception("module php-ldap isn't install");
        }
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        /** @var SecurityExtension $extension */
        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new FormLoginLdapFactory());
        $extension->addSecurityListenerFactory(new HttpBasicLdapFactory());
    }
}
