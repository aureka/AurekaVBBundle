<?php

namespace Aureka\VBBundle\Tests;

use Aureka\VBBundle\VBUserRepository;

class VBUserRepositoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function itCreatesUsers()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $connection = $this->getMockBuilder('Doctrine\DBAL\Connection')->disableOriginalConstructor()->getMock();
        $repository = new VBUserRepository($request, $connection, 'vb_');

        $connection->expects($this->once(0))
            ->method('insert')
            ->with('vb_user', array('username' => 'some_name'));

        $repository->create('some_name');
    }

}