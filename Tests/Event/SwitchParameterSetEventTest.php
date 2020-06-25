<?php
/**
 * Created by PhpStorm.
 * User: boellmann
 * Date: 02.10.18
 * Time: 14:44
 */

namespace DoL\LdapBundle\Event;


class SwitchParameterSetEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SwitchParameterSetEvent
     */
    private $event;
    /**
     * @var array
     */
    private $parameter;

    protected function setUp()
    {
        $this->parameter = [
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
        ];
        $this->event = new SwitchParameterSetEvent($this->parameter);
    }


    public function testGetter()
    {
        $this->assertTrue(is_array($this->event->getParameterSet()));
        $this->assertArraySubset($this->parameter,$this->event->getParameterSet());
    }

}
