<?php

namespace Aureka\VBBundle;


class VBUsers
{

    private $db;


    public function __construct(VBDatabase $db)
    {
        $this->db = $db;
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


    public function updateSession(VBSession $session)
    {
        $session->refresh($this->db);
        return $this;
    }
}
