<?php

namespace Aureka\VBBundle;

class VBUser
{
    public $username;

    public static function fromArray(array $data)
    {
        return new static($data['username']);
    }
}
