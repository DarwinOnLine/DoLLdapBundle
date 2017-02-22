<?php

namespace DoL\LdapBundle\Ldap;

use DoL\LdapBundle\Driver\LdapDriverInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use DoL\LdapBundle\Hydrator\HydratorInterface;

/**
 * Ldap manager for multi-domains.
 *
 * @author DarwinOnLine
 * @author Maks3w
 *
 * @see https://github.com/DarwinOnLine/DoLLdapBundle
 */
class LdapManager implements LdapManagerInterface
{
    protected $driver;
    protected $paramSets = [];

    protected $params = [];
    protected $ldapAttributes = [];
    protected $ldapUsernameAttr;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    public function __construct(LdapDriverInterface $driver, HydratorInterface $hydrator, array $paramSets)
    {
        $this->driver = $driver;
        $this->hydrator = $hydrator;
        $this->paramSets = $paramSets;
    }

    /**
     * {@inheritdoc}
     */
    public function bind(UserInterface $user, $password)
    {
        if (!empty($this->params)) {
            return $this->driver->bind($user, $password);
        } else {
            foreach ($this->paramSets as $paramSet) {
                $this->driver->init($paramSet['driver']);

                if (false !== $this->driver->bind($user, $password)) {
                    $this->params = $paramSet['user'];
                    $this->setLdapAttr();

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByUsername($username)
    {
        if (!empty($this->params)) {
            return $this->findUserBy([$this->ldapUsernameAttr => $username]);
        } else {
            foreach ($this->paramSets as $paramSet) {
                $this->driver->init($paramSet['driver']);
                $this->params = $paramSet['user'];
                $this->setLdapAttr();

                $user = $this->findUserBy([$this->ldapUsernameAttr => $username]);
                if (false !== $user && $user instanceof UserInterface) {
                    return $user;
                }

                $this->params = [];
                $this->setLdapAttr();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findUserBy(array $criteria)
    {
        if (!empty($this->params)) {
            $filter = $this->buildFilter($criteria);
            $entries = $this->driver->search($this->params['baseDn'], $filter);
            if ($entries['count'] > 1) {
                throw new \Exception('This search can only return a single user');
            }

            if ($entries['count'] == 0) {
                return null;
            }
            $user = $this->hydrator->hydrate($entries[0]);

            return $user;
        } else {
            foreach ($this->paramSets as $paramSet) {
                $this->driver->init($paramSet['driver']);
                $this->params = $paramSet['user'];
                $this->setLdapAttr();

                $user = $this->findUserBy($criteria);
                if (false !== $user && $user instanceof UserInterface) {
                    return $user;
                }

                $this->params = [];
                $this->setLdapAttr();
            }
        }
    }

    /**
     * Sets temp Ldap attributes.
     */
    private function setLdapAttr()
    {
        if (isset($this->params['attributes'])) {
            $this->hydrator->setAttributeMap($this->params['attributes']);

            foreach ($this->params['attributes'] as $attr) {
                $this->ldapAttributes[] = $attr['ldap_attr'];
            }

            $this->ldapUsernameAttr = $this->ldapAttributes[0];
        } else {
            $this->ldapAttributes = [];
            $this->ldapUsernameAttr = null;
        }
    }

    /**
     * Build Ldap filter.
     *
     * @param array  $criteria
     * @param string $condition
     *
     * @return string
     */
    protected function buildFilter(array $criteria, $condition = '&')
    {
        $filters = [];
        if (isset($this->params['filter'])) {
            $filters[] = $this->params['filter'];
        }
        foreach ($criteria as $key => $value) {
            $value = ldap_escape($value, '', LDAP_ESCAPE_FILTER);
            $filters[] = sprintf('(%s=%s)', $key, $value);
        }

        return sprintf('(%s%s)', $condition, implode($filters));
    }
}
