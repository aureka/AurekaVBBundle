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
        $ip = implode('.', array_slice(explode('.', $this->request->getClientIp()), 0, 4 - $this->ipCheck));
        $now = time();
        $idhash = md5($this->request->headers->get('User-Agent') . $ip);
        $sessionhash = md5($now . $this->request->getUri() . $idhash . $this->request->server->get('REMOTE_ADDR') . $this->randomPassword(6));
        $this->db->insert('session', array(
            'sessionhash' => $sessionhash,
            'userid' => $user->id,
            'host' => substr($this->request->server->get('REMOTE_ADDR'), 0, 15),
            'idhash' => $idhash,
            'lastactivity' => $now,
            'location' => '/forum/',
            'useragent' => $this->request->headers->get('User-Agent'),
            'loggedin' => 2,
            ));
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
