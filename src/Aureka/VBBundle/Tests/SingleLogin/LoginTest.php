<?php

namespace Aureka\VBBundle\Tests\SingleLogin;

use Aureka\VBBundle\Tests\Application\AppKernel;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{

    private $dispatcher;

    protected static function createKernel(array $options = array())
    {
        return new AppKernel('test', true);
    }


    public function setUp()
    {
        $container = self::createClient()->getKernel()->getContainer();
        $this->dispatcher = $container->get('event_dispatcher');
    }


    /**
     * @test
     */
    public function itRuns()
    {

    }
}