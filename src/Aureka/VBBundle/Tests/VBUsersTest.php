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
        $this->db = $this->aDoubleOf('Aureka\VBBundle\VBDatabase');
        $this->users = new VBUsers($this->db, 'vb_session_', 1, 'license');
    }

    /**
     * @test
     */
    public function itCreatesUsers()
    {
        $user_data = array('username' => 'some_name', 'password' => 'some_password');
        $session = $this->aDoubleOf('Aureka\VBBundle\VBSession');

        $this->db->expects($this->once())
            ->method('insert')
            ->with('user', $user_data);

        $this->users->create($session, 'some_name', 'some_password');
    }


    /**
     * @test
     */
    public function itLoadsAUserByUsername()
    {
        $session = $this->aDoubleOf('Aureka\VBBundle\VBSession');

        $this->db->expects($this->once())
            ->method('load')
            ->with('user', array('username' => 'some_name'))
            ->will($this->returnValue(array('userid' => 1, 'username' => 'some_name', 'password' => '')));

        $this->users->load($session, 'some_name');
    }


    /**
     * @test
     */
    public function itRefreshesTheSessionInTheDatabase()
    {
        $session = $this->aDoubleOf('Aureka\VBBundle\VBSession');
        $session->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(5));
        $session->expects($this->any())
            ->method('export')
            ->will($this->returnValue(array()));

        $this->db->expects($this->once())
            ->method('delete')
            ->with('session', array('idhash' => 5));
        $this->db->expects($this->once())
            ->method('insert')
            ->with('session', array());

        $this->users->updateSession($session);
    }


    private function aDoubleOf($class_name)
    {
        return $this->getMockBuilder($class_name)->disableOriginalConstructor()->getMock();
    }


}
