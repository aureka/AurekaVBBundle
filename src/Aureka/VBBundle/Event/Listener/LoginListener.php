<?php

namespace Aureka\VBBundle\Event\Listener;

use Aureka\VBBundle\VBUsers,
    Aureka\VBBundle\VBConfiguration;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Component\HttpFoundation\Request;

class LoginListener
{

    private $repository;
    private $requestStack;

    private $justLoggedIn;


    public function __construct(VBUsers $repository, RequestStack $request_stack)
    {
        $this->repository = $repository;
        $this->requestStack = $request_stack;
    }


    public static function createFor(VBConfiguration $config, RequestStack $request_stack)
    {
        return new static(VBUsers::createFor($config), $request_stack);
    }


    public function onUserLogin(AuthenticationEvent $event)
    {
        $username = $event->getAuthenticationToken()->getUsername();
        $this->repository->connect();
        $user = $this->repository->load($username) ?: $this->repository->create($username);
        $this->justLoggedIn = $user;
    }


    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!is_null($this->justLoggedIn)) {
            $this->repository->login($this->justLoggedIn, $event->getResponse());
            $this->justLoggedIn = null;
        }
    }

}
