<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DoL\LdapBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @author DarwinOnLine
 * @author Maks3w
 * @link https://github.com/DarwinOnLine/DoLLdapBundle
 */
class Unique extends Constraint
{
    public $message = 'The value for "%property%" already exists.';
    public $property;

    public function getDefaultOption()
    {
        return 'property';
    }

    public function getRequiredOptions()
    {
        return array('property');
    }

    public function validatedBy()
    {
        return 'dol_ldap.validator.unique';
    }

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
