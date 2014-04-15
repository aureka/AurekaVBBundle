<?php

namespace Aureka\VBBundle\Event\Listener;

use Aureka\VBBundle\VBUsers;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Component\HttpFoundation\Request;

class LoginListener
{

    private $repository;
    private $requestStack;


    public function __construct(VBUsers $repository, RequestStack $request_stack)
    {
        $this->repository = $repository;
        $this->requestStack = $request_stack;
    }


    public function onUserLogin(AuthenticationEvent $event)
    {
        $username = $event->getAuthenticationToken()->getUsername();
        $user = $this->repository->load($username) ?: $this->repository->create($username);
        $this->repository->login($user, $this->getCurrentRequest());
    }


    private function getCurrentRequest()
    {
        return $this->requestStack->getCurrentRequest() ?: new Request();
    }

}
