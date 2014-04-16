<?php

namespace Aureka\VBBundle\Tests;

use Aureka\VBBundle\VBUsers,
    Aureka\VBBundle\VBUser;
use Symfony\Component\HttpFoundation\Request;

class VBUsersTest extends \PHPUnit_Framework_TestCase
{

    private $db;
    private $users;


    public function setUp()
    {
        $this->db = $this->getMockBuilder('Aureka\VBBundle\VBDatabase')->disableOriginalConstructor()->getMock();
        $this->users = new VBUsers($this->db, 'vb_session_', 1);
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
    public function itDeletesTheSessionWhenLoggingIn()
    {
        $request = new Request;
        $user = VBUser::fromArray(array('userid' => 1, 'username' => 'some_name', 'password' => ''));
        $request->cookies->set('vb_session_sessionhash', 'SomeHash');

        $this->db->expects($this->once())
            ->method('delete')
            ->with('session', array('sessionhash' => 'SomeHash'));

        $this->users->login($user, $request);
    }


    /**
     * @test
     */
    public function itStoresANewSessionHashWhenLoggingIn()
    {
        $user = VBUser::fromArray(array('userid' => 1, 'username' => 'some_name', 'password' => ''));

        $this->db->expects($this->once())
            ->method('insert');

        $this->users->login($user, new Request);
    }


    /**
     * @test
     */
    public function itCreatesACookieWhenLoggingIn()
    {
        $request = new Request;
        $user = VBUser::fromArray(array('userid' => 1, 'username' => 'some_name', 'password' => ''));

        $this->users->login($user, $request);

        $this->assertNotNull($request->cookies->get('vb_session_sessionhash'));
    }

}
