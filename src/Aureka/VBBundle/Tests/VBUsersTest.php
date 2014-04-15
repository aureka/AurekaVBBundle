<?php

namespace Aureka\VBBundle\Tests;

use Aureka\VBBundle\VBUsers;

class VBUsersTest extends \PHPUnit_Framework_TestCase
{

    private $connection;
    private $users;


    public function setUp()
    {
        $this->request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $this->connection = $this->getMockBuilder('Doctrine\DBAL\Connection')->disableOriginalConstructor()->getMock();
        $this->users = new VBUsers($this->request, $this->connection, 'vb_');
    }

    /**
     * @test
     */
    public function itCreatesUsers()
    {
        $this->connection->expects($this->once())
            ->method('insert')
            ->with('vb_user', array('username' => 'some_name'));

        $this->users->create('some_name');
    }


    /**
     * @test
     */
    public function itLoadsAUserByUsername()
    {
        $this->connection->expects($this->once())
            ->method('fetchAssoc')
            ->with('SELECT * FROM vb_user WHERE username = ?', array('some_name'));

        $this->users->load('some_name');
    }

}