<?php

namespace Aureka\VBBundle\Event\Listener;

use Aureka\VBBundle\VBUsers,
    Aureka\VBBundle\VBSession;
use Aureka\VBBundle\Exception\VBUserException;

use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

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


    public function onUserLogin(AuthenticationEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        try {
            $this->userToLogIn = $this->userProvider->load($this->session, $user->getUsername());
        } catch (VBUserException $e) {
            $this->userToLogIn = $this->userProvider->create($this->session, $user->getUsername(), $user->getPassword(), $user->getId());
        }
    }


    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!is_null($this->userToLogIn)) {
            $this->session->login($event->getResponse());
            $this->userProvider->updateSession($this->session);
            $this->userToLogIn = null;
        }
    }
}
