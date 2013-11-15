<?php

namespace DoL\LdapBundle\Tests\DependencyInjection;

use DoL\LdapBundle\DependencyInjection\DoLLdapExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DoLLdapExtensionTest extends \PHPUnit_Framework_TestCase
{

    /** @var ContainerBuilder */
    public $container;

    public function testConfigurationNamespace()
    {
        $this->container = new ContainerBuilder();
        $this->container->registerExtension(new DoLLdapExtension());
        $this->assertTrue($this->container->hasExtension('dol_ldap'));
    }

    public function testLoadMinimalConfiguration()
    {
        $minRequiredConfig = array(
            'domains' => array(
                'server1' => array(
                    'driver' => array(
                        'host' => 'ldap.hostname.local',
                    ),
                    'user' => array(
                        'baseDn' => 'ou=Persons,dc=example,dc=com',
                    ),
                ),
            ),
        );

        $defaultConfig = $this->getDefaultConfig();

        $this->container = new ContainerBuilder();
        $extension = new DoLLdapExtension();

        $extension->load(array($minRequiredConfig), $this->container);

        $this->assertHasDefinition('dol_ldap.ldap_driver');
        $this->assertHasDefinition('dol_ldap.ldap_manager.default');
        
        $this->assertParameter($defaultConfig['domains'], 'dol_ldap.domains.parameters');

        $this->assertAlias('fos_user.user_manager', 'dol_ldap.user_manager');
        $this->assertAlias('dol_ldap.ldap_manager.default', 'dol_ldap.ldap_manager');
        $this->assertAlias('dol_ldap.ldap_driver.zend', 'dol_ldap.ldap_driver');
    }

    public function testLoadFullConfiguration()
    {
        $config                                                 = $this->getDefaultConfig();
        $config['domains']['server1']['driver']['username']     = null;
        $config['domains']['server1']['driver']['password']     = null;
        $config['domains']['server1']['driver']['optReferrals'] = false;

        $this->container = new ContainerBuilder();
        $extension = new DoLLdapExtension();

        $extension->load(array($config), $this->container);

        $this->assertParameter($config['domains'], 'dol_ldap.domains.parameters');
    }

    public function testLoadDriverConfiguration()
    {
        $config                                                         = $this->getDefaultConfig();
        $config['domains']['server1']['driver']['accountFilterFormat']  = '(%(uid=%s))';

        $this->container = new ContainerBuilder();
        $extension = new DoLLdapExtension();

        $extension->load(array($config), $this->container);

        $this->assertParameter($config['domains'], 'dol_ldap.domains.parameters');
    }

    public function testSslConfiguration()
    {
        $config                                                 = $this->getDefaultConfig();
        $config['domains']['server1']['driver']['useSsl']       = true;
        $config['domains']['server1']['driver']['useStartTls']  = false;

        $this->container = new ContainerBuilder();
        $extension = new DoLLdapExtension();

        $extension->load(array($config), $this->container);

        $this->assertParameter($config['domains'], 'dol_ldap.domains.parameters');
    }

    public function testTlsConfiguration()
    {
        $config                                                 = $this->getDefaultConfig();
        $config['domains']['server1']['driver']['useSsl']       = false;
        $config['domains']['server1']['driver']['useStartTls']  = true;

        $this->container = new ContainerBuilder();
        $extension = new DoLLdapExtension();

        $extension->load(array($config), $this->container);

        $this->assertParameter($config['domains'], 'dol_ldap.domains.parameters');
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testSslTlsExclusiveConfiguration()
    {
        $config                                                 = $this->getDefaultConfig();
        $config['domains']['server1']['driver']['useSsl']       = true;
        $config['domains']['server1']['driver']['useStartTls']  = true;

        $this->container = new ContainerBuilder();
        $extension = new DoLLdapExtension();

        $extension->load(array($config), $this->container);
    }

    private function getDefaultConfig()
    {
        return array(
            'domains' => array(
                'server1' => array(
                    'driver' => array(
                        'host'                => 'ldap.hostname.local',
                        'port'                => 389,
                        'useSsl'              => false,
                        'useStartTls'         => false,
                        'baseDn'              => 'ou=Persons,dc=example,dc=com',
                        'accountFilterFormat' => '',
                        'bindRequiresDn'      => false,
                    ),
                    'user'                => array(
                        'baseDn'     => 'ou=Persons,dc=example,dc=com',
                        'filter'     => '',
                        'attributes' => array(
                            array(
                                'ldap_attr'   => 'uid',
                                'user_method' => 'setUsername',
                            ),
                        ),
                    ),
                ),
            ),
            'service'     => array(
                'user_manager' => 'fos_user.user_manager',
                'ldap_manager' => 'dol_ldap.ldap_manager.default',
                'ldap_driver'  => 'dol_ldap.ldap_driver.zend',
            ),
        );
    }

    private function assertAlias($value, $key)
    {
        $this->assertEquals($value, (string) $this->container->getAlias($key), sprintf('%s alias is not correct', $key));
    }

    private function assertParameter($value, $key)
    {
        $this->assertEquals($value, $this->container->getParameter($key), sprintf('%s parameter is not correct', $key));
    }

    private function assertHasDefinition($id)
    {
        $this->assertTrue(($this->container->hasDefinition($id) ? : $this->container->hasAlias($id)), sprintf('%s definition is not set', $id));
    }

    protected function tearDown()
    {
        unset($this->container);
    }
}
