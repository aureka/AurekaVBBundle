<?php

namespace Aureka\VBBundle;

use Aureka\VBBundle\Exception\VBUserException,
    Aureka\VBBundle\Exception\VBSessionException;


class VBUsers
{

    private $db;
    private $defaultUserGroup;


    public function __construct(VBDatabase $db, $defaultUserGroup)
    {
        $this->db = $db;
        $this->defaultUserGroup = $defaultUserGroup;
    }


    public function create(VBSession $session, $username, $password = null)
    {
        $this->db->connect();
        $id = $this->db->insert('user', array(
            'username' => $username,
            'password' => $password,
            'usergroupid' => $this->defaultUserGroup));
        return $this->getUserInstance($session, $username, $password, $id);
    }


    public function load(VBSession $session, $username)
    {
        $this->db->connect();
        $data = $this->db->load('user', array('username' => $username));
        if (!$data) {
            throw new VBUserException(sprintf('Unable to load data for user with username %s', $username));
        }
        return $this->getUserInstance($session, $username, $data['password'], $data['userid']);
    }


    private function getUserInstance(VBSession $session, $username, $password, $id)
    {
        $user = new VBUser($username, $password, $id);
        $user->setSession($session);
        return $user;
    }



    public function updateSession(VBSession $session)
    {
        if (is_null($session->getId())) {
            throw new VBSessionException('Unable to update a session that is not initialized');
        }
        $this->removeSession($session);
        $this->db->insert('session', $session->export());
        return $this;
    }


    public function removeSession(VBSession $session)
    {
        if (is_null($session->getId())) {
            throw new VBSessionException('Unable to delete a session that is not initialized');
        }
        $this->db->connect();
        $this->db->delete('session', array('idhash' => $session->getId()));
        return $this;
    }
}
