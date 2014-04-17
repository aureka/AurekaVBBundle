<?php

namespace Aureka\VBBundle;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Cookie;

class VBSession
{

    const LOCATION = '/forum/';

    private $request;
    private $config;

    private $userId;
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


    public function toArray()
    {
        return array(
            'sessionhash' => $this->sessionHash,
            'userid' => $this->userId,
            'host' => $this->host,
            'idhash' => $this->hashId,
            'lastactivity' => $this->lastActivity,
            'location' => $this->location,
            'useragent' => $this->userAgent,
            'loggedin' => $this->loggedIn,
            );
    }


    public function login(VBUser $user, Response $response)
    {
        $this->initialize($user, $response);
        $now = time();
        $this->setCookie($response, 'sessionhash', $this->sessionHash);
        $this->setCookie($response, 'lastvisit', $now);
        $this->setCookie($response, 'lastactivity', $now);
        $this->setCookie($response, 'userid', $this->userId);
        $this->setCookie($response, 'password', md5($user->password.$this->config->license));
        return $this;
    }


    private function initialize(VBUser $user, Response $response)
    {
        $this->userId = $user->id;
        $this->host = $this->request->getHost();
        $ip = implode('.', array_slice(explode('.', $this->request->getClientIp()), 0, 4-$this->config->ipCheck));
        $this->userAgent = $this->request->headers->get('User-Agent');
        $this->hashId = md5($this->userAgent, $ip);
        $this->location = self::LOCATION;
        $this->lastActivity = time();
        $this->loggedIn = 2;
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
        return md5('/forum/' . $this->hashId . $this->randomPassword(6));
    }


    private function randomPassword($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomPassword = '';
        for ($i = 0; $i < $length; $i++) {
            $randomPassword .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomPassword;
    }


    private function prefix($cookie_name)
    {
        return $this->config->cookiePrefix.$cookie_name;
    }
}
