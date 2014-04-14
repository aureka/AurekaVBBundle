<?php

namespace Aureka\VBBundle\Tests\SingleLogin;

use Aureka\VBBundle\Tests\Application\AppKernel;
use Symfony\Component\Security\Core\AuthenticationEvents,
    Symfony\Component\Security\Core\Event\AuthenticationEvent;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{

    private $container;
    private $dispatcher;

    protected static function createKernel(array $options = array())
    {
        return new AppKernel('test', true);
    }


    public function setUp()
    {
        $this->container = self::createClient()->getKernel()->getContainer();
        $this->dispatcher = $this->container->get('event_dispatcher');
    }


    /**
     * @test
     */
    public function itCreatesANewUserInVBulletinIfNotExists()
    {
        $vb_bridge = $this->getMock('Aureka\VBBundle\Bridge');
        $this->container->set('aureka_vb.bridge', $vb_bridge);
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue('test_username'));
        $event = new AuthenticationEvent($token);

        $vb_bridge->expects($this->once())
            ->method('createUser')
            ->with('test_username');

        $this->dispatcher->dispatch(AuthenticationEvents::AUTHENTICATION_SUCCESS, $event);
    }



    /**
     * @test
     */
    public function itDoesNotCreateAUserInVBulletinIfAlreadyExists()
    {
        $vb_bridge = $this->getMock('Aureka\VBBundle\Bridge');
        $vb_bridge->expects($this->any())
            ->method('loadUser')
            ->will($this->returnValue(true));
        $this->container->set('aureka_vb.bridge', $vb_bridge);
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue('test_username'));
        $event = new AuthenticationEvent($token);

        $vb_bridge->expects($this->never())
            ->method('createUser');

        $this->dispatcher->dispatch(AuthenticationEvents::AUTHENTICATION_SUCCESS, $event);
    }
}
