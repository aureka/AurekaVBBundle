<?php

namespace Aureka\VBBundle\Tests;

use Aureka\VBBundle\VBUsers,
    Aureka\VBBundle\VBUser;

class VBUsersTest extends \PHPUnit_Framework_TestCase
{

    private $db;
    private $users;


    public function setUp()
    {
        $this->db = $this->getMockBuilder('Aureka\VBBundle\VBDatabase')->disableOriginalConstructor()->getMock();
        $this->users = new VBUsers($this->db, 'vb_session_', 1, 'license');
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
            ->will($this->returnValue(array('userid' => 1, 'username' => 'some_name', 'password' => '')));

        $this->users->load('some_name');
    }

}
