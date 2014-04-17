<?php

namespace Aureka\VBBundle\Event\Listener;

use Aureka\VBBundle\VBUsers,
    Aureka\VBBundle\VBConfiguration,
    Aureka\VBBundle\VBSession;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Component\HttpFoundation\Request;

class LoginListener
{

    private $repository;
    private $session;

    private $justLoggedIn;


    public function __construct(VBUsers $repository, VBSession $session)
    {
        $this->repository = $repository;
        $this->session = $session;
    }


    public static function createFor(VBConfiguration $config, RequestStack $request_stack)
    {
        $request = $request_stack->getMasterRequest() ?: new Request();
        $session = new VBSession($request, $config);
        $repository = new VBUsers($config->createDB());
        return new static($repository, $session);
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
            $this->session->login($this->justLoggedIn, $event->getResponse());
            $this->repository->updateUserSession($this->justLoggedIn, $this->session);
            $this->justLoggedIn = null;
        }
    }
}
