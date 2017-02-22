<?php

namespace DoL\LdapBundle\Hydrator;

use DoL\LdapBundle\Model\LdapUserInterface;

/**
 * Provide a hydrator template for easy implementation.
 *
 * @author DarwinOnLine
 * @author Maks3w
 *
 * @see https://github.com/DarwinOnLine/DoLLdapBundle
 */
abstract class AbstractHydrator implements HydratorInterface
{
    use HydrateWithMapTrait;

    /**
     * @var string[]
     */
    private $attributeMap;

    /**
     * {@inheritdoc}
     */
    public function setAttributeMap(array $attributeMap)
    {
        $this->attributeMap = $attributeMap['attributes'];
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(array $ldapEntry)
    {
        $user = $this->createUser();

        $this->hydrateUserWithAttributesMap($user, $ldapEntry, $this->attributeMap);

        if ($user instanceof LdapUserInterface) {
            $user->setDn($ldapEntry['dn']);
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    abstract protected function createUser();
}
