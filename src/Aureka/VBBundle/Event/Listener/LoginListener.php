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
        $username = $event->getAuthenticationToken()->getUsername();
        if (!$this->bridge->loadUser($username)) {
            $this->bridge->createUser($username);
        }
    }

}