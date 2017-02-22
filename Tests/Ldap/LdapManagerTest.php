<?php

namespace DoL\LdapBundle\Tests\Ldap;

use DoL\LdapBundle\Hydrator\HydratorInterface;
use DoL\LdapBundle\Ldap\LdapManager;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @covers \DoL\LdapBundle\Ldap\LdapManager
 */
class LdapManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var array */
    protected $paramSets;

    /**
     * @var \DoL\LdapBundle\Driver\LdapDriverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $driver;

    /**
     * @var HydratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $hydrator;

    /**
     * @var LdapManager
     */
    protected $ldapManager;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->paramSets = [
            'server1' => [
                'driver' => [
                    // SOME ATTRIBUTES
                ],
                'user' => [
                    'baseDn' => 'ou=Groups,dc=example,dc=com',
                    'filter' => '(attr0=value0)',
                    'attributes' => [
                        [
                            'ldap_attr' => 'uid',
                            'user_method' => 'setUsername',
                        ],
                    ],
                ],
            ],
        ];

        $this->driver = $this->getMock('DoL\LdapBundle\Driver\LdapDriverInterface');

        $this->hydrator = $this->getMock('DoL\LdapBundle\Hydrator\HydratorInterface');

        $this->ldapManager = new LdapManager($this->driver, $this->hydrator, $this->paramSets);
    }

    /**
     * @covers \DoL\LdapBundle\Ldap\LdapManager::findUserByUsername
     */
    public function testFindUserByUsername()
    {
        $username = 'test_username';

        $ldapResponse = $this->ldapResponse($username);

        $this->driver
            ->expects($this->once())
            ->method('search')
            ->with($this->equalTo('ou=Groups,dc=example,dc=com'),
                $this->equalTo('(&(attr0=value0)(uid=test_username))')
            )
            ->will($this->returnValue($ldapResponse))
        ;

        $resultUser = $this->ldapManager->findUserByUsername($username);

        self::assertEquals($username, $resultUser->getUsername());
    }

    /**
     * @covers \DoL\LdapBundle\Ldap\LdapManager::findUserBy
     */
    public function testFindUserBy()
    {
        $username = 'test_username';

        $ldapResponse = $this->ldapResponse($username);

        $this->driver
            ->expects($this->once())
            ->method('search')
            ->with($this->equalTo('ou=Groups,dc=example,dc=com'),
                $this->equalTo('(&(attr0=value0)(uid=test_username))')
            )
            ->will($this->returnValue($ldapResponse))
        ;

        $criteria = ['uid' => 'test_username'];
        $resultUser = $this->ldapManager->findUserBy($criteria);

        self::assertEquals($username, $resultUser->getUsername());
    }

    /**
     * @covers \DoL\LdapBundle\Ldap\LdapManager::buildFilter
     */
    public function testBuildFilter()
    {
        $reflectionClass = new \ReflectionClass('DoL\LdapBundle\Ldap\LdapManager');
        $method = $reflectionClass->getMethod('buildFilter');
        $method->setAccessible(true);
        $params = $reflectionClass->getProperty('params');
        $params->setAccessible(true);
        $params->setValue($this->ldapManager, $this->paramSets['server1']['user']);

        $criteria = array(
            'attr1' => 'value1',
            'attr2' => 'value2',
        );
        $expected = '(&(attr0=value0)(attr1=value1)(attr2=value2))';

        self::assertEquals($expected, $method->invoke($this->ldapManager, $criteria));
    }

    /**
     * @covers \DoL\LdapBundle\Ldap\LdapManager::bind
     */
    public function testBind()
    {
        $password = 'password';

        /** @var UserInterface $user */
        $user = $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserInterface');

        $this->driver->expects($this->once())
            ->method('bind')
            ->with($user, $this->equalTo($password))
            ->will($this->returnValue(true));

        self::assertTrue($this->ldapManager->bind($user, $password));
    }

    /**
     * @param $username
     *
     * @return array
     */
    protected function ldapResponse($username)
    {
        $entry = [
            'dn' => 'ou=group, dc=host, dc=foo',
            'uid' => [
                'count' => 1,
                0 => $username,
            ],
        ];

        $entries = [
            'count' => 1,
            $entry,
        ];

        $user = $this->getMock('Symfony\\Component\\Security\\Core\\User\\UserInterface');
        $user->expects($this->any())
            ->method('getUsername')
            ->willReturn($username)
        ;

        $this->hydrator->expects($this->once())
            ->method('hydrate')
            ->with($entry)
            ->willReturn($user)
        ;

        return $entries;
    }
}
