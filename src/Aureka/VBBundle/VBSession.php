<?php

namespace Aureka\VBBundle;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Cookie;

class VBSession
{

    const LOCATION = '/forum/';

    public $userId;
    public $clientIp;
    public $host;
    public $userAgent;
    public $location;
    public $lastActivity;
    public $loggedIn;

    private $headers;
    private $cookiePrefix;
    private $sessionHash;

    public function __construct(ResponseHeaderBag $headers, $cookie_prefix)
    {
        $this->headers = $headers;
        $this->cookiePrefix = $cookie_prefix;
    }


    /**
     * @todo fill in client ip and host
     */
    public static function createFor(Response $response, VBUser $user, $ip_check, $cookie_prefix)
    {
        $session = new static($response->headers, $cookie_prefix);
        $session->userId = $user->id;
        $session->clientIp = '';
        $session->host = '';
        $session->userAgent = $response->headers->get('User-Agent');
        $session->location = self::LOCATION;
        $session->lastActivity = time();
        $session->loggedIn = 2;
        return $session->initialize();
    }


    public function initialize()
    {
        $this->sessionHash = $this->createSessionHash();
        return $this;
    }


    public function toArray()
    {
        return array(
            'sessionhash' => $this->sessionHash,
            'userid' => $this->userId,
            'host' => $this->host,
            'idhash' => $this->getHashId(),
            'lastactivity' => $this->lastActivity,
            'location' => $this->location,
            'useragent' => $this->userAgent,
            'loggedin' => $this->loggedIn,
            );
    }


    public function login()
    {
        $now = time();
        $this->setCookie('sessionhash', $this->sessionHash);
        $this->setCookie('lastvisit', $now);
        $this->setCookie('lastactivity', $now);
        $this->setCookie('userid', $this->userId);
        $this->setCookie('password', '');
        return $this;
    }



    public function hasCookie($cookie_name)
    {
        return null !== $this->getCookie($cookie_name);
    }


    public function getCookie($cookie_name)
    {
        $cookies = $this->headers->getCookies();
        foreach ($cookies as $cookie) {
            if ($cookie->getName() == $this->prefix($cookie_name)) {
                return $cookie->getValue();
            }
        }
        return null;
    }


    public function setCookie($cookie_name, $value)
    {
        $cookie = new Cookie($this->prefix($cookie_name), $value);
        return $this->headers->setCookie($cookie);
    }


    private function getHashId()
    {
        return md5($this->userAgent . $this->clientIp);
    }


    private function createSessionHash()
    {
        return md5($this->lastActivity . '/forum/' . $this->getHashId() . $this->host . $this->randomPassword(6));
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
        return $this->cookiePrefix.$cookie_name;
    }
}
