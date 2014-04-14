<?php

namespace Aureka\VBBundle\Event\Listener;

use Aureka\VBBundle\Bridge;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

class LoginListener
{

    private $bridge;


    public function __construct(Bridge $bridge)
    {
        $this->bridge = $bridge;
    }


    public function onUserLogin(AuthenticationEvent $event)
    {
        $token = $event->getAuthenticationToken();
        $this->bridge->createUser($token->getUsername());
    }

}