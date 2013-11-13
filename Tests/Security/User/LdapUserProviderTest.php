<?php

namespace DoL\LdapBundle\Tests\Security\User;

use DoL\LdapBundle\Security\User\LdapUserProvider;
use DoL\LdapBundle\Tests\TestUser;

class LdapUserProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \DoL\LdapBundle\Ldap\LdapManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ldapManager;

    /**
     * @var \DoL\LdapBundle\Security\User\LdapUserProvider
     */
    protected $userProvider;

    protected function setUp()
    {
        $this->ldapManager = $this->getMockBuilder('DoL\LdapBundle\Ldap\LdapManager')
                ->disableOriginalConstructor()
                ->getMock();

        $this->userProvider = new LdapUserProvider($this->ldapManager);
    }

    public function testLoadUserByUsername()
    {
        $username = 'test_username';
        $user     = new TestUser();
        $user->setUsername($username);

        $this->ldapManager->expects($this->once())
                ->method('findUserByUsername')
                ->with($this->equalTo($username))
                ->will($this->returnValue($user));

        $this->assertEquals($username, $this->userProvider->loadUserByUsername($username)->getUsername());
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testLoadUserByUsernameNotFound()
    {
        $username = 'invalid_username';

        $this->ldapManager->expects($this->once())
                ->method('findUserByUsername')
                ->will($this->returnValue(null));

        $this->userProvider->loadUserByUsername($username);
    }

    public function testRefreshUser()
    {
        $username = 'test_username';
        $user     = new TestUser();
        $user->setUsername($username);

        $this->ldapManager->expects($this->once())
                ->method('findUserByUsername')
                ->with($this->equalTo($username))
                ->will($this->returnValue($user));

        $this->assertEquals($user, $this->userProvider->refreshUser($user));
    }
}
?>
