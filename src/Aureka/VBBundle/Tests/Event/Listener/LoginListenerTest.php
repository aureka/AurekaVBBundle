<?php

namespace Aureka\VBBundle\Tests\Event\Listener;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\AuthenticationEvents,
    Symfony\Component\Security\Core\Event\AuthenticationEvent;

use Aureka\VBBundle\Event\Listener\LoginListener;


class LoginListenerTest extends \PHPUnit_Framework_TestCase
{

    private $listener;
    private $repository;

    public function setUp()
    {
        $this->repository = $this->getMockBuilder('Aureka\VBBundle\VBUsers')->disableOriginalConstructor()->getMock();
        $this->listener = new LoginListener($this->repository, new RequestStack);
    }


    /**
     * @test
     */
    public function itCreatesANewUserInVBulletinIfNotExists()
    {
        $this->mockVBUsers(array('load' => false, 'create' => $this->aUser()));
        $event = $this->getAuthenticationEventForUser('test_username');

        $this->repository->expects($this->once())
            ->method('create')
            ->with('test_username');

        $this->listener->onUserLogin($event);
    }


    /**
     * @test
     */
    public function itDoesNotCreateAUserInVBulletinIfAlreadyExists()
    {
        $this->mockVBUsers(array('load' => $this->aUser()));
        $event = $this->getAuthenticationEventForUser('test_username');

        $this->repository->expects($this->never())
            ->method('create');

        $this->listener->onUserLogin($event);
    }


    /**
     * @test
     */
    public function itPerformsTheLoginInVbulletin()
    {
        $user = $this->aUser();
        $vb_bridge = $this->mockVBUsers(array('load' => $user));
        $event = $this->getAuthenticationEventForUser('test_username');

        $this->repository->expects($this->once())
            ->method('login')
            ->with($user);

        $this->listener->onUserLogin($event);
    }


    private function getAuthenticationEventForUser($username)
    {
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue('test_username'));
        return new AuthenticationEvent($token);
    }


    private function mockVBUsers(array $stubs = array())
    {
        foreach ($stubs as $method => $return_value) {
            $this->repository->expects($this->any())
                ->method($method)
                ->will($this->returnValue($return_value));
        }
    }


    private function aUser()
    {
        return $this->getMockBuilder('Aureka\VBBundle\VBUser')->disableOriginalConstructor()->getMock();
    }
}
