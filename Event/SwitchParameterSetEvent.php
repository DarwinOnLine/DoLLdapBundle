<?php
/**
 * Created by PhpStorm.
 * User: boellmann
 * Date: 02.10.18
 * Time: 14:38
 */

namespace DoL\LdapBundle\Event;


use Symfony\Component\EventDispatcher\Event;

class SwitchParameterSetEvent extends Event
{
    const PARAMETERSET = 'dol_ldap.manager.switch_parameter_set';

    private $parameterSet = [];

    /**
     * SwitchParameterSet constructor.
     * @param array $parameterSet
     */
    public function __construct(array $parameterSet)
    {
        $this->parameterSet = $parameterSet;
    }

    /**
     * @return array
     */
    public function getParameterSet(): array
    {
        return $this->parameterSet;
    }

}
