<?php

namespace Aureka\VBBundle;

use Symfony\Component\HttpFoundation\Response;

class VBUser
{

    const DEFAULT_GROUP = 2;

    private $username;
    private $password;
    private $id;

    private $session;

    public function __construct($username, $password = null, $id = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->id = $id;
    }


    public function getId()
    {
        return $this->id;
    }


    public function getPassword()
    {
        return $this->password;
    }


    public function getUsername()
    {
        return $this->username;
    }


    public function setSession(VBSession $session)
    {
        $session->setUser($this);
        $this->session = $session;
        return $this;
    }


    public function export()
    {
        return array(
            'userid' => $this->id,
            'username' => $this->username,
            'password' => $this->password
            );
    }


    public function login(Response $response)
    {
        $this->session->login($response);
    }
}
