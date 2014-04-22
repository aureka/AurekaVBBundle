<?php

namespace Aureka\VBBundle;

use Aureka\VBBundle\Exception\VBUserException,
    Aureka\VBBundle\Exception\VBSessionException;


class VBUsers
{

    private $db;


    public function __construct(VBDatabase $db)
    {
        $this->db = $db;
    }


    public function create(VBSession $session, $username, $password = null, $id = null)
    {
        $user = new VBUser($username, $password, $id);
        $user->setSession($session);
        return $user;
    }


    public function load(VBSession $session, $username)
    {
        $this->db->connect();
        $data = $this->db->load('user', array('username' => $username));
        if (!$data) {
            throw new VBUserException(sprintf('Unable to load data for user with username %s', $username));
        }
        return $this->create($session, $username, $data['password'], $data['userid']);
    }


    public function persist(VBUser $user)
    {
        $this->db->connect();
        $this->db->insert('user', $user->export());
        return $this;
    }


    public function updateSession(VBSession $session)
    {
        if (is_null($session->getId())) {
            throw new VBSessionException('Unable to update a session that is not initialized');
        }
        $this->db->connect();
        $this->db->delete('session', array('idhash' => $session->getId()));
        $this->db->insert('session', $session->export());
        return $this;
    }
}
