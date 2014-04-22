<?php

namespace Aureka\VBBundle;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Cookie;

use Aureka\VBBundle\Exception\VBSessionException;

class VBSession
{

    const LOCATION = '/forum/';
    const INITIAL_LOGIN_STATUS = 2;

    private $request;
    private $config;
    private $user;

    private $hashId;
    private $host;
    private $userAgent;
    private $location;
    private $lastActivity;
    private $loggedIn;
    private $sessionHash;


    public function __construct(Request $request, VBConfiguration $config)
    {
        $this->request = $request;
        $this->config = $config;
    }


    public function getId()
    {
        return $this->hashId;
    }


    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }


    public function login(Response $response)
    {
        $this->initialize($response);
        $now = time();
        $this->setCookie($response, 'sessionhash', $this->sessionHash);
        $this->setCookie($response, 'lastvisit', $now);
        $this->setCookie($response, 'lastactivity', $now);
        $this->setCookie($response, 'userid', $this->user->getId());
        $this->setCookie($response, 'password', md5($this->user->getPassword().$this->config->license));
        return $this;
    }


    public function export()
    {
        return array(
            'sessionhash' => $this->sessionHash,
            'userid' => $this->user->getId(),
            'host' => $this->host,
            'idhash' => $this->hashId,
            'lastactivity' => $this->lastActivity,
            'location' => $this->location,
            'useragent' => $this->userAgent,
            'loggedin' => $this->loggedIn,
            );
    }


    private function initialize(Response $response)
    {
        $this->host = $this->request->server->get('SERVER_ADDR');
        $ip = implode('.', array_slice(explode('.', $this->request->getClientIp()), 0, 4-$this->config->ipCheck));
        $this->userAgent = $this->request->headers->get('User-Agent');
        $this->hashId = md5($this->userAgent.$ip);
        $this->location = self::LOCATION;
        $this->lastActivity = time();
        $this->loggedIn = self::INITIAL_LOGIN_STATUS;
        $this->sessionHash = $this->createSessionHash();
        return $this;
    }


    private function setCookie(Response $response, $cookie_name, $value)
    {
        $cookie = new Cookie($this->prefix($cookie_name), $value, 0, '/', null, false, false);
        return $response->headers->setCookie($cookie);
    }


    private function createSessionHash()
    {
        return md5(self::LOCATION . $this->hashId . uniqid());
    }


    private function prefix($cookie_name)
    {
        return $this->config->cookiePrefix.$cookie_name;
    }
}
