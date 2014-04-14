<?php

namespace Aureka\VBBundle;

use Symfony\Component\HttpFoundation\RequestStack;

class Bridge
{

    private $requestStack;


    public function __construct(RequestStack $request_stack)
    {
        $this->requestStack = $request_stack;
    }


    public function createUser($username)
    {

    }


    public function loadUser($username)
    {

    }


    public function login($username)
    {
        $request = $this->requestStack->getCurrentRequest();
    }
}
