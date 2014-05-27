<?php

namespace Aureka\VBBundle\Event\Listener;

use Aureka\VBBundle\VBUsers,
    Aureka\VBBundle\VBSession;
use Aureka\VBBundle\Exception\VBUserException;

use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent,
    FOS\UserBundle\Event\FilterUserResponseEvent;

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


    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        return $this->storeUser($user);
    }


    public function onAuthenticationSuccess(AuthenticationEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        return $this->storeUser($user);
    }


    public function onFOSUserRegistration(FilterUserResponseEvent $event)
    {
        return $this->storeUser($event->getUser());
    }


    private function storeUser(UserInterface $user)
    {
        try {
            $this->userToLogIn = $this->userProvider->load($this->session, $user->getUsername());
        } catch (VBUserException $e) {
            $this->userToLogIn = $this->userProvider->create($this->session, $user->getUsername(), $user->getPassword());
        }
        return $this;
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
