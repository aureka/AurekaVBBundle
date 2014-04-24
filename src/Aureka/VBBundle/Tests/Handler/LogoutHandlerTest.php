<?php

namespace Aureka\VBBundle\Tests\Handler;

use Aureka\VBBundle\Handler\LogoutHandler;


class LogoutHandlerTest extends \PHPUnit_Framework_TestCase
{

    private $provider;
    private $session;
    private $logoutHandler;


    public function setUp()
    {
        $this->provider = $this->aDoubleOf('Aureka\VBBundle\VBUsers');
        $this->session = $this->aDoubleOf('Aureka\VBBundle\VBSession');
        $this->logoutHandler = new LogoutHandler($this->provider, $this->session);
    }


    /**
     * @test
     */
    public function itDeletesTheSessionInTheDatabase()
    {
        $this->provider->expects($this->once())
            ->method('removeSession')
            ->with($this->session);

        $this->doLogout();
    }


    /**
     * @test
     */
    public function itDeletesTheCookies()
    {
        $this->session->expects($this->once())
            ->method('logout');

        $this->doLogout();
    }


    private function doLogout()
    {
        $request = $this->aDoubleOf('Symfony\Component\HttpFoundation\Request');
        $response = $this->aDoubleOf('Symfony\Component\HttpFoundation\Response');
        $token = $this->aDoubleOf('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->logoutHandler->logout($request, $response, $token);
    }




    private function aDoubleOf($class_name)
    {
        return $this->getMockBuilder($class_name)->disableOriginalConstructor()->getMock();
    }

}