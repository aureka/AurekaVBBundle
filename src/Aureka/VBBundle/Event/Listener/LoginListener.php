<?php

namespace Aureka\VBBundle\Event\Listener;

use Aureka\VBBundle\VBUserRepository;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

class LoginListener
{

    private $repository;


    public function __construct(VBUserRepository $repository)
    {
        $this->repository = $repository;
    }


    public function onUserLogin(AuthenticationEvent $event)
    {
        $username = $event->getAuthenticationToken()->getUsername();
        if (!$this->repository->load($username)) {
            $this->repository->create($username);
        }
        $this->repository->login($username);
    }

}
