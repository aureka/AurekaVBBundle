<?php

namespace Aureka\VBBundle\Event\Listener;

use Aureka\VBBundle\VBUsers;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

class LoginListener
{

    private $repository;


    public function __construct(VBUsers $repository)
    {
        $this->repository = $repository;
    }


    public function onUserLogin(AuthenticationEvent $event)
    {
        $username = $event->getAuthenticationToken()->getUsername();
        $user = $this->repository->load($username) ?: $this->repository->create($username);
        $this->repository->login($user);
    }

}
