<?php

namespace Aureka\VBBundle\Tests\Event\Listener;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\AuthenticationEvents,
    Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\HttpFoundation\Response;

use Aureka\VBBundle\Event\Listener\LoginListener;


class LoginListenerTest extends \PHPUnit_Framework_TestCase
{

    private $listener;
    private $session;

    public function setUp()
    {
        $this->repository = $this->getMockBuilder('Aureka\VBBundle\VBUsers')->disableOriginalConstructor()->getMock();
        $this->session = $this->getMockBuilder('Aureka\VBBundle\VBSession')->disableOriginalConstructor()->getMock();
        $this->listener = new LoginListener($this->repository, $this->session);
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
        $authentication_event = $this->getAuthenticationEventForUser('test_username');
        $response_event = $this->getResponseEvent();

        $this->session->expects($this->once())
            ->method('login')
            ->with($user);

        $this->listener->onUserLogin($authentication_event);
        $this->listener->onKernelResponse($response_event);
    }


    /**
     * @test
     */
    public function itUpdatesTheUserSessionInTheDatabase()
    {
        $user = $this->aUser();
        $vb_bridge = $this->mockVBUsers(array('load' => $user));
        $authentication_event = $this->getAuthenticationEventForUser('test_username');
        $response_event = $this->getResponseEvent();

        $this->repository->expects($this->once())
            ->method('updateUserSession')
            ->with($user, $this->session);

        $this->listener->onUserLogin($authentication_event);
        $this->listener->onKernelResponse($response_event);
    }


    private function getAuthenticationEventForUser($username)
    {
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue('test_username'));
        return new AuthenticationEvent($token);
    }

    private function getResponseEvent()
    {
        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\FilterResponseEvent')->disableOriginalConstructor()->getMock();
        $event->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue(new Response));
        return $event;
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
