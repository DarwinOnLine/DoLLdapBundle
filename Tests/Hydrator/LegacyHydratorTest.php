<?php

namespace DoL\LdapBundle\Tests\Hydrator;

use FOS\UserBundle\Model\UserManagerInterface;
use DoL\LdapBundle\Hydrator\LegacyHydrator;
use DoL\LdapBundle\Tests\TestUser;

class LegacyHydratorTest extends AbstractHydratorTestCase
{
    protected function setUp()
    {
        parent::setUp();

        /** @var UserManagerInterface|\PHPUnit_Framework_MockObject_MockObject $userManager */
        $userManager = $this->getMock('FOS\UserBundle\Model\UserManagerInterface');
        $userManager->expects($this->any())
            ->method('createUser')
            ->will($this->returnValue(new TestUser()));

        $this->hydrator = new LegacyHydrator($userManager);
        $this->hydrator->setAttributeMap($this->getDefaultUserConfig());
    }
}
