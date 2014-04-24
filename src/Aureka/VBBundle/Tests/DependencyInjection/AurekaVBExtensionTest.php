<?php

namespace Aureka\VBBundle\Tests\DependencyInjection;

use Aureka\VBBundle\Tests\Application\AppKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Aureka\VBBundle\VBUser;

class LoginListenerTest extends WebTestCase
{

    private $container;


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
    public function itDefinesADefaultVBulletinConfiguration()
    {
        $config = $this->container->get('aureka_vb.configuration');

        $this->assertInstanceOf('Aureka\VBBundle\VBConfiguration', $config);
        $this->assertEquals($config->defaultUserGroup, VBUser::DEFAULT_GROUP);
        $this->assertEquals($config->license, 'somelicense');
        $this->assertEquals($config->dbDriver, 'pdo_mysql');
        $this->assertEquals($config->dbHost, 'localhost');
        $this->assertEquals($config->dbName, 'test_db');
        $this->assertEquals($config->dbPort, null);
        $this->assertEquals($config->dbUser, 'test_user');
        $this->assertEquals($config->dbPassword, 'test_password');
        $this->assertEquals($config->dbPrefix, 'vb3_');
        $this->assertEquals($config->ipCheck, '1');
        $this->assertEquals($config->cookiePrefix, 'vb_');
    }


    /**
     * @test
     */
    public function itDefinesALoginListener()
    {
        $listener = $this->container->get('aureka_vb.login_listener');

        $this->assertInstanceOf('Aureka\VBBundle\Event\Listener\LoginListener', $listener);
    }


    /**
     * @test
     */
    public function itDefinesALogoutHandler()
    {
        $handler = $this->container->get('aureka_vb.logout_handler');

        $this->assertInstanceOf('Aureka\VBBundle\Handler\LogoutHandler', $handler);
    }
}