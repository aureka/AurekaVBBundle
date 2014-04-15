<?php

namespace Aureka\VBBundle;

class VBUser
{
    public $id;
    public $username;
    public $password;


    public function __construct($id, $username, $password)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
    }

    public static function fromArray(array $data)
    {
        return new static($data['id'], $data['username'], $data['password']);
    }
}
