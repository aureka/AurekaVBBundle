<?php

namespace Aureka\VBBundle\Tests;

use Aureka\VBBundle\VBUsers,
    Aureka\VBBundle\VBUser;
use Symfony\Component\HttpFoundation\Request;

class VBUsersTest extends \PHPUnit_Framework_TestCase
{

    private $request;
    private $db;
    private $users;


    public function setUp()
    {
        $this->request = new Request;
        $this->db = $this->getMockBuilder('Aureka\VBBundle\VBDatabase')->disableOriginalConstructor()->getMock();
        $this->users = new VBUsers($this->request, $this->db, 'vb_session_', 1);
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
            ->will($this->returnValue(array('id' => 1, 'username' => 'some_name', 'password' => '')));

        $this->users->load('some_name');
    }


    /**
     * @test
     */
    public function itDeletesTheSessionWhenLoggingIn()
    {
        $user = VBUser::fromArray(array('id' => 1, 'username' => 'some_name', 'password' => ''));
        $this->request->cookies->set('vb_session_sessionhash', 'SomeHash');

        $this->db->expects($this->once())
            ->method('delete')
            ->with('session', array('sessionhash' => 'SomeHash'));

        $this->users->login($user);
    }


    /**
     * @test
     */
    public function itStoresANewSessionHashWhenLoggingIn()
    {
        $user = VBUser::fromArray(array('id' => 1, 'username' => 'some_name', 'password' => ''));

        $this->db->expects($this->once())
            ->method('insert');

        $this->users->login($user);
    }


    /**
     * @test
     */
    public function itCreatesACookieWhenLoggingIn()
    {
        $user = VBUser::fromArray(array('id' => 1, 'username' => 'some_name', 'password' => ''));

        $this->users->login($user);

        $this->assertNotNull($this->request->cookies->get('vb_session_sessionhash'));
    }

}
