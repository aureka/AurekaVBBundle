<?php

namespace Aureka\VBBundle;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RequestStack;

class VBUsers
{

    private $request;
    private $db;


    public function __construct(Request $request, VBDatabase $db)
    {
        $this->request = $request;
        $this->db = $db;
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
    }


    public function login($username)
    {
    }

}
