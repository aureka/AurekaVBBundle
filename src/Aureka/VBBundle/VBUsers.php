<?php

namespace Aureka\VBBundle;

use Symfony\Component\HttpFoundation\Response;

class VBUsers
{

    private $db;
    private $cookiePrefix;
    private $ipCheck;
    private $license;


    public function __construct(VBDatabase $db, $cookie_prefix, $ip_check, $license)
    {
        $this->db = $db;
        $this->cookiePrefix = $cookie_prefix;
        $this->ipCheck = $ip_check;
    }


    public static function createFor(VBConfiguration $config)
    {
        return new static($config->createDB(), $config->cookiePrefix, $config->ipCheck, $config->license);
    }


    public function connect()
    {
        $this->db->connect();
        return $this;
    }


    public function create($username)
    {
        $this->db->insert('user', array('username' => $username));
        return $this->load($username);
    }


    public function load($username)
    {
        $data = $this->db->load('user', array('username' => $username));
        return $data ? VBUser::fromArray($data) : false;
    }


    public function login(VBUser $user, Response $response)
    {
        $session = VBSession::createFor($response, $user, $this->ipCheck, $this->cookiePrefix, $this->license );
        if ($current_hash = $session->getCookie('sessionhash')) {
            $this->db->delete('session', array('sessionhash' => $current_hash));
        }
        $this->db->insert('session', $session->toArray());
        $session->login($user);
        return $this;
    }
}
