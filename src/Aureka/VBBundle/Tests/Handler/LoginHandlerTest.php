<?php

namespace Aureka\VBBundle\Tests\Handler;

use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\HttpFoundation\Response;

use Aureka\VBBundle\Handler\LoginHandler;
use Aureka\VBBundle\Exception\VBUserException;


class LoginHandlerTest extends \PHPUnit_Framework_TestCase
{

    private $handler;
    private $provider;
    private $user;
    private $session;

    public function setUp()
    {
        $this->provider = $this->getMockBuilder('Aureka\VBBundle\VBUsers')->disableOriginalConstructor()->getMock();
        $this->session = $this->getMockBuilder('Aureka\VBBundle\VBSession')->disableOriginalConstructor()->getMock();
        $this->user = $this->aDoubleOf('Symfony\Component\Security\Core\User\UserInterface', array(
            'getUsername' => 'test_username'));
        $this->handler = new LoginHandler($this->provider, $this->session);
    }


    /**
     * @test
     */
    public function itCreatesANewUserInVBulletinIfNotExists()
    {
        $this->mockVBUsers(array('load' => false, 'create' => $this->aDoubleOf('Aureka\VBBundle\VBUser')));
        $this->provider->expects($this->any())
            ->method('load')
            ->with($this->session, 'test_username')
            ->will($this->throwException(new VBUserException));

        $this->provider->expects($this->once())
            ->method('create')
            ->with($this->session, 'test_username');

        $this->doLogin();
    }


    /**
     * @test
     */
    public function itDoesNotCreateAUserInVBulletinIfAlreadyExists()
    {
        $this->mockVBUsers(array('load' => $this->aDoubleOf('Aureka\VBBundle\VBUser')));

        $this->provider->expects($this->never())
            ->method('create');

        $this->doLogin();
    }


    /**
     * @test
     */
    public function itPerformsTheLoginInVbulletin()
    {
        $vb_bridge = $this->mockVBUsers(array('load' => $this->aDoubleOf('Aureka\VBBundle\VBUser')));
        $response_event = $this->getResponseEvent();
        $this->doLogin();

        $this->session->expects($this->once())
            ->method('login')
            ->with($response_event->getResponse());

        $this->handler->onKernelResponse($response_event);
    }


    /**
     * @test
     */
    public function itUpdatesTheDatabaseSessionWhenLogginIn()
    {
        $user = $this->aDoubleOf('Aureka\VBBundle\VBUser');
        $vb_bridge = $this->mockVBUsers(array('load' => $user));
        $response_event = $this->getResponseEvent();
        $this->doLogin();

        $this->provider->expects($this->once())
            ->method('updateSession')
            ->with($this->session);

        $this->handler->onKernelResponse($response_event);
    }


    private function doLogin()
    {
        $request = $this->aDoubleOf('Symfony\Component\HttpFoundation\Request');
        $token = $this->aDoubleOf('Symfony\Component\Security\Core\Authentication\Token\TokenInterface', array(
            'getUser' => $this->user));
        $this->handler->onAuthenticationSuccess($request, $token);
    }


    private function getResponseEvent()
    {
        return $this->aDoubleOf('Symfony\Component\HttpKernel\Event\FilterResponseEvent', array(
            'getResponse' => new Response));
    }


    private function mockVBUsers(array $stubs = array())
    {
        foreach ($stubs as $method => $return_value) {
            $this->provider->expects($this->any())
                ->method($method)
                ->will($this->returnValue($return_value));
        }
    }

    private function aDoubleOf($class_name, array $stubs = array())
    {
        $double = $this->getMockBuilder($class_name)->disableOriginalConstructor()->getMock();
        foreach ($stubs as $method => $returnValue) {
            $double->expects($this->any())
                ->method($method)
                ->will($this->returnValue($returnValue));
        }
        return $double;
    }


}
