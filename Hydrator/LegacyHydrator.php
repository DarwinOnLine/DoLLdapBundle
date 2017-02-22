<?php

namespace DoL\LdapBundle\Hydrator;

use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * Populate a FOSUserBundle user with data from LDAP.
 *
 * @author DarwinOnLine
 * @author Maks3w
 * @link https://github.com/DarwinOnLine/DoLLdapBundle
 */
final class LegacyHydrator extends AbstractHydrator
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @param UserManagerInterface $userManager
     */
    public function __construct($userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function createUser()
    {
        $user = $this->userManager->createUser();
        $user->setPassword('');

        if ($user instanceof AdvancedUserInterface) {
            $user->setEnabled(true);
        }

        return $user;
    }
}
