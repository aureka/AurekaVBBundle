<?php

namespace Aureka\VBBundle\Tests;

use Aureka\VBBundle\Bridge;

class BridgeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function itCreatesUsers()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $connection = $this->getMockBuilder('Doctrine\DBAL\Connection')->disableOriginalConstructor()->getMock();
        $bridge = new Bridge($request, $connection, 'vb_');

        $connection->expects($this->once(0))
            ->method('insert')
            ->with('vb_user', array('username' => 'some_name'));

        $bridge->createUser('some_name');
    }

}