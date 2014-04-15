<?php

namespace Aureka\VBBundle\Tests;

use Aureka\VBBundle\VBUsers;

class VBUsersTest extends \PHPUnit_Framework_TestCase
{

    private $db;
    private $users;


    public function setUp()
    {
        $this->request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $this->db = $this->getMockBuilder('Aureka\VBBundle\VBDatabase')->disableOriginalConstructor()->getMock();
        $this->users = new VBUsers($this->request, $this->db);
    }

    /**
     * @test
     */
    public function itCreatesUsers()
    {
        $this->db->expects($this->once())
            ->method('insert')
            ->with('user', array('username' => 'some_name'));

        $this->users->create('some_name');
    }


    /**
     * @test
     */
    public function itLoadsAUserByUsername()
    {
        $this->db->expects($this->once())
            ->method('load')
            ->with('user', array('username' => 'some_name'))
            ->will($this->returnValue(array('username' => 'some_name')));

        $this->users->load('some_name');
    }


    /**
     * @test
     */
    public function itLoginsAUser()
    {
    }

}