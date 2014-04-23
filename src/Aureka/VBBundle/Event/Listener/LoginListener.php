<?php

namespace Aureka\VBBundle\Event\Listener;

use Aureka\VBBundle\VBUsers,
    Aureka\VBBundle\VBConfiguration,
    Aureka\VBBundle\VBSession;
use Aureka\VBBundle\Exception\VBUserException;

use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class LoginListener
{

    private $userProvider;
    private $session;

    private $userToLogIn;


    public function __construct(VBUsers $user_provider, VBSession $session)
    {
        $this->userProvider = $user_provider;
        $this->session = $session;
    }


    public static function createFor(VBConfiguration $config, RequestStack $request_stack)
    {
        $user_provider = new VBUsers($config->createDB());
        $request = $request_stack->getMasterRequest() ?: new Request();
        return new static($user_provider, $config->createSession($request));
    }


    public function onUserLogin(AuthenticationEvent $event)
    {
        $username = $event->getAuthenticationToken()->getUsername();
        try {
            $this->userToLogIn = $this->userProvider->load($this->session, $username);
        } catch (VBUserException $e) {
            $this->userToLogIn = $this->userProvider->create($this->session, $username);
        }
    }


    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!is_null($this->userToLogIn)) {
            $this->userToLogIn->login($event->getResponse());
            $this->userProvider->updateSession($this->session);
            $this->userToLogIn = null;
        }
    }
}
