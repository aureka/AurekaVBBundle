<?php

namespace Aureka\VBBundle;

use Symfony\Component\HttpFoundation\Request;

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

    private $cookies;
    private $cookiePrefix;
    private $sessionHash;

    public function __construct($cookies, $cookie_prefix)
    {
        $this->cookies = $cookies;
        $this->cookiePrefix = $cookie_prefix;
    }


    public static function fromRequest(Request $request, VBUser $user, $ip_check, $cookie_prefix)
    {
        $session = new static($request->cookies, $cookie_prefix);
        $session->userId = $user->id;
        $session->clientIp = $request->getClientIp();
        $session->host = substr($request->server->get('REMOTE_ADDR'), 0, 15);
        $session->userAgent = $request->headers->get('User-Agent');
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
        return $this->cookies->has($this->cookiePrefix.$cookie_name);
    }


    public function getCookie($cookie_name)
    {
        return $this->cookies->get($this->cookiePrefix.$cookie_name);
    }


    public function setCookie($cookie_name, $value)
    {
        return $this->cookies->set($this->cookiePrefix.$cookie_name, $value);
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
}
