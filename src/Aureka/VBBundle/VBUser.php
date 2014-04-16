<?php

namespace Aureka\VBBundle;

class VBUser
{

    const DEFAULT_GROUP = 2;

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
        return new static($data['userid'], $data['username'], $data['password']);
    }
}
