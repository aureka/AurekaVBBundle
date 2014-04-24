<?php

namespace Aureka\VBBundle\Tests;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

use Aureka\VBBundle\VBSession,
    Aureka\VBBundle\VBConfiguration;

class VBSessionTest extends \PHPUnit_Framework_TestCase
{

    private $session;
    private $configuration;
    private $request;


    public function setUp()
    {
        $this->configuration = $this->aDoubleOf('Aureka\VBBundle\VBConfiguration');
        $request = new Request();
        $this->session = new VBSession($request, $this->configuration);
    }


    /**
     * @test
     */
    public function itSetsTheProperSessionCookiesWhenLoggingIn()
    {
        $response = new Response();
        $this->session->setUser($this->aDoubleOf('Aureka\VBBundle\VBUser'));

        $this->session->login($response);

        $this->assertCookieExists('sessionhash', $response);
        $this->assertCookieExists('lastvisit', $response);
        $this->assertCookieExists('lastactivity', $response);
        $this->assertCookieExists('userid', $response);
        $this->assertCookieExists('password', $response);
    }


    /**
     * @test
     */
    public function itRemovesSessionCookiesWhenLoggingOut()
    {
        $response = new Response();

        $this->session->logout($response);

        $this->assertCookieDoesNotExist('sessionhash', $response);
        $this->assertCookieDoesNotExist('lastvisit', $response);
        $this->assertCookieDoesNotExist('lastactivity', $response);
        $this->assertCookieDoesNotExist('userid', $response);
        $this->assertCookieDoesNotExist('password', $response);
    }


    private function aDoubleOf($class_name)
    {
        return $this->getMockBuilder($class_name)->disableOriginalConstructor()->getMock();
    }


    private function assertCookieExists($cookie_name, Response $response)
    {
        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === $cookie_name) {
                return;
            }
        }
        return $this->fail(sprintf('The cookie %s was not found in the response object.', $cookie_name));
    }

    private function assertCookieDoesNotExist($cookie_name, Response $response)
    {
        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === $cookie_name) {
                $this->fail(sprintf('The cookie %s was not supposed to be found in the response object.', $cookie_name));
            }
        }
        return;
    }
}
