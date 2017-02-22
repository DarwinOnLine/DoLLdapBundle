<?php

namespace DoL\LdapBundle\Tests\Security\User;

use DoL\LdapBundle\Security\User\LdapUserProvider;
use DoL\LdapBundle\Tests\TestUser;
use FR3D\Psr3MessagesAssertions\PhpUnit\TestLogger;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * @covers \DoL\LdapBundle\Security\User\LdapUserProvider
 */
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

        $this->userProvider = new LdapUserProvider($this->ldapManager, new TestLogger());
    }

    public function testLoadUserByUsername()
    {
        $username = 'test_username';
        $user = new TestUser();
        $user->setUsername($username);

        $this->ldapManager->expects($this->once())
            ->method('findUserByUsername')
            ->with($this->equalTo($username))
            ->will($this->returnValue($user));

        self::assertEquals($username, $this->userProvider->loadUserByUsername($username)->getUsername());
    }

    public function testLoadUserByUsernameNotFound()
    {
        $username = 'invalid_username';

        $this->ldapManager->expects($this->once())
            ->method('findUserByUsername')
            ->will($this->returnValue(null));

        try {
            $this->userProvider->loadUserByUsername($username);
            self::fail('Expected Symfony\Component\Security\Core\Exception\UsernameNotFoundException to be thrown');
        } catch (UsernameNotFoundException $notFoundException) {
            self::assertEquals($username, $notFoundException->getUsername());
        }
    }

    public function testRefreshUser()
    {
        $username = 'test_username';
        $user = new TestUser();
        $user->setUsername($username);

        $this->ldapManager->expects($this->once())
            ->method('findUserByUsername')
            ->with($this->equalTo($username))
            ->will($this->returnValue($user));

        self::assertEquals($user, $this->userProvider->refreshUser($user));
    }
}
