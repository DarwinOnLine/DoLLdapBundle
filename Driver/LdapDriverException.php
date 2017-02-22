<?php

namespace DoL\LdapBundle\Driver;

/**
 * Driver exception.
 *
 * @author DarwinOnLine
 * @author Maks3w
 *
 * @see https://github.com/DarwinOnLine/DoLLdapBundle
 */
class LdapDriverException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
