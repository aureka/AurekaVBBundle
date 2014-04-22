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


    /**
     * @test
     */
    public function itUpdatesAUserSession()
    {
        $session = $this->getMockBuilder('Aureka\VBBundle\VBSession')->disableOriginalConstructor()->getMock();
        $session->expects($this->any())
            ->method('toArray')
            ->will($this->returnValue(array('idhash' => 'SomeSessionId')));

        $this->db->expects($this->once())
            ->method('delete')
            ->with('session', array('idhash' => 'SomeSessionId'));
        $this->db->expects($this->once())
            ->method('insert')
            ->with('session', array('idhash' => 'SomeSessionId'));

        $this->users->updateSession($session);
    }

}
