<?php

namespace Aureka\VBBundle;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RequestStack;

class VBUsers
{

    private $request;
    private $db;
    private $cookiePrefix;
    private $ipCheck;


    public function __construct(Request $request, VBDatabase $db, $cookie_prefix, $ip_check)
    {
        $this->request = $request;
        $this->db = $db;
        $this->cookiePrefix = $cookie_prefix;
        $this->ipCheck = $ip_check;
    }


    public static function createForDB(RequestStack $request_stack, array $db_params, $db_prefix)
    {
        $db = VBDatabase::create($db_params, $db_prefix);
        return new static($request_stack->getCurrentRequest(), $db);
    }


    public function create($username)
    {
        $this->db->insert('user', array('username' => $username));
        return $this;
    }


    public function load($username)
    {
        $data = $this->db->load('user', array('username' => $username));
        return $data ? VBUser::fromArray($data) : false;
    }


    public function login(VBUser $user)
    {
        if ($current_hash = $this->request->cookies->get($this->cookiePrefix.'sessionhash')) {
            $this->db->delete('session', array('sessionhash' => $current_hash));
        }
        $session = VBSession::fromRequest($this->request, $user, $this->ipCheck);
        $this->db->insert('session', $session->toArray());
    }
}
