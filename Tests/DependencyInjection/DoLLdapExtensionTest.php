<?php

namespace DoL\LdapBundle\Tests\DependencyInjection;

use DoL\LdapBundle\DependencyInjection\DoLLdapExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DoLLdapExtensionTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTrait;

    /** @var ContainerBuilder */
    public $container;

    public function testConfigurationNamespace()
    {
        $this->container = new ContainerBuilder();
        $this->container->registerExtension(new DoLLdapExtension());
        self::assertTrue($this->container->hasExtension('dol_ldap'));
    }

    public function testLoadMinimalConfiguration()
    {
        $minRequiredConfig = [
            'domains' => [
                'server1' => [
                    'driver' => [
                        'host' => 'ldap.hostname.local',
                    ],
                    'user' => [
                        'baseDn' => 'ou=Persons,dc=example,dc=com',
                    ],
                ],
            ],
        ];

        $defaultConfig = $this->getDefaultConfig();

        $this->container = new ContainerBuilder();
        $extension = new DoLLdapExtension();

        $extension->load([$minRequiredConfig], $this->container);

        self::assertHasDefinition('dol_ldap.ldap_driver');
        self::assertHasDefinition('dol_ldap.ldap_manager.default');

        self::assertParameter($defaultConfig['domains'], 'dol_ldap.domains.parameters');

        self::assertAlias('dol_ldap.user_hydrator.default', 'dol_ldap.user_hydrator');
        self::assertAlias('dol_ldap.ldap_manager.default', 'dol_ldap.ldap_manager');
        self::assertAlias('dol_ldap.ldap_driver.zend', 'dol_ldap.ldap_driver');
    }

    public function testLoadFullConfiguration()
    {
        $config = $this->getDefaultConfig();
        $config['domains']['server1']['driver']['username']     = null;
        $config['domains']['server1']['driver']['password']     = null;
        $config['domains']['server1']['driver']['optReferrals'] = false;

        $this->container = new ContainerBuilder();
        $extension = new DoLLdapExtension();

        $extension->load([$config], $this->container);

        self::assertEquals($config['domains'], $this->container->getParameter('dol_ldap.domains.parameters'));
    }

    public function testLoadDriverConfiguration()
    {
        $config = $this->getDefaultConfig();
        $config['domains']['server1']['driver']['accountFilterFormat'] = '(%(uid=%s))';

        $this->container = new ContainerBuilder();
        $extension = new DoLLdapExtension();

        $extension->load([$config], $this->container);

        self::assertEquals($config['domains']['server1']['driver'], $this->container->getParameter('dol_ldap.ldap_driver.parameters'));
        self::assertEquals($config['domains']['server1']['user'], $this->container->getParameter('dol_ldap.ldap_manager.parameters'));
    }

    public function testSslConfiguration()
    {
        $config = $this->getDefaultConfig();
        $config['domains']['server1']['driver']['useSsl'] = true;
        $config['domains']['server1']['driver']['useStartTls'] = false;

        $this->container = new ContainerBuilder();
        $extension = new DoLLdapExtension();

        $extension->load([$config], $this->container);

        self::assertEquals($config['domains']['server1']['driver'], $this->container->getParameter('dol_ldap.ldap_driver.parameters'));
    }

    public function testTlsConfiguration()
    {
        $config = $this->getDefaultConfig();
        $config['domains']['server1']['driver']['useSsl'] = false;
        $config['domains']['server1']['driver']['useStartTls'] = true;

        $this->container = new ContainerBuilder();
        $extension = new DoLLdapExtension();

        $extension->load([$config], $this->container);

        self::assertEquals($config['domains']['server1']['driver'], $this->container->getParameter('dol_ldap.ldap_driver.parameters'));
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testSslTlsExclusiveConfiguration()
    {
        $config = $this->getDefaultConfig();
        $config['domains']['server1']['driver']['useSsl'] = true;
        $config['domains']['server1']['driver']['useStartTls'] = true;

        $this->container = new ContainerBuilder();
        $extension = new DoLLdapExtension();

        $extension->load([$config], $this->container);
    }

    private function assertAlias($value, $key)
    {
        self::assertEquals($value, (string) $this->container->getAlias($key), sprintf('%s alias is not correct', $key));
    }

    private function assertParameter($value, $key)
    {
        self::assertEquals($value, $this->container->getParameter($key), sprintf('%s parameter is not correct', $key));
    }

    private function assertHasDefinition($id)
    {
        self::assertTrue(($this->container->hasDefinition($id) ?: $this->container->hasAlias($id)), sprintf('%s definition is not set', $id));
    }

    protected function tearDown()
    {
        unset($this->container);
    }
}
