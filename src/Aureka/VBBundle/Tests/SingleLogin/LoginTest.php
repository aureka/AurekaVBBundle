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
        $vb_bridge = $this->mockVBUserRepository();
        $event = $this->getAuthenticationEventForUser('test_username');

        $vb_bridge->expects($this->once())
            ->method('create')
            ->with('test_username');

        $this->dispatcher->dispatch(AuthenticationEvents::AUTHENTICATION_SUCCESS, $event);
    }


    /**
     * @test
     */
    public function itDoesNotCreateAUserInVBulletinIfAlreadyExists()
    {
        $vb_bridge = $this->mockVBUserRepository(array('load' => true));
        $event = $this->getAuthenticationEventForUser('test_username');

        $vb_bridge->expects($this->never())
            ->method('create');

        $this->dispatcher->dispatch(AuthenticationEvents::AUTHENTICATION_SUCCESS, $event);
    }


    /**
     * @test
     */
    public function itPerformsTheLoginInVbulletin()
    {
        $user = $this->getMock('Aureka\VBBundle\VBUser');
        $vb_bridge = $this->mockVBUserRepository(array('load' => $user));
        $event = $this->getAuthenticationEventForUser('test_username');

        $vb_bridge->expects($this->once())
            ->method('login')
            ->with('test_username');

        $this->dispatcher->dispatch(AuthenticationEvents::AUTHENTICATION_SUCCESS, $event);
    }


    private function getAuthenticationEventForUser($username)
    {
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue('test_username'));
        return new AuthenticationEvent($token);
    }


    private function mockVBUserRepository(array $stubs = array())
    {
        $vb_bridge = $this->getMockBuilder('Aureka\VBBundle\VBUserRepository')
            ->disableOriginalConstructor()
            ->getMock();
        foreach ($stubs as $method => $return_value) {
            $vb_bridge->expects($this->any())
                ->method($method)
                ->will($this->returnValue($return_value));
        }
        $this->container->set('aureka_vb.repository', $vb_bridge);
        return $vb_bridge;
    }
}
