<?php

namespace Aureka\VBBundle;

use Symfony\Component\HttpFoundation\Request;

class VBSession
{

    public $userId;
    public $clientIp;
    public $host;
    public $userAgent;
    public $location;
    public $lastActivity;
    public $loggedIn;

    public function __construct($user_id, $client_ip, $host, $useragent, $location, $lastactivity, $loggedin)
    {
        $this->userId = $user_id;
        $this->clientIp = $client_ip;
        $this->host = $host;
        $this->userAgent = $useragent;
        $this->location = $location;
        $this->lastActivity = $lastactivity;
        $this->loggedIn = $loggedin;
    }


    public static function fromRequest(Request $request, VBUser $user, $ip_check)
    {
        return new static(
            $user->id,
            $request->getClientIp(),
            substr($request->server->get('REMOTE_ADDR'), 0, 15),
            $request->headers->get('User-Agent'),
            '/forum/',
            time(),
            2
            );
    }

    public function toArray()
    {
        return array(
            'sessionhash' => $this->getSessionHash(),
            'userid' => $this->userId,
            'host' => $this->host,
            'idhash' => $this->getHashId(),
            'lastactivity' => $this->lastActivity,
            'location' => $this->location,
            'useragent' => $this->userAgent,
            'loggedin' => $this->loggedIn,
            );
    }

    private function getHashId()
    {
        return md5($this->userAgent . $this->clientIp);
    }


    private function getSessionHash()
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
