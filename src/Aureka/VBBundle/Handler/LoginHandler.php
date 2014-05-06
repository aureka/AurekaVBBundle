<?php

namespace Aureka\VBBundle\Handler;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

use Aureka\VBBundle\VBUsers,
    Aureka\VBBundle\VBSession;
use Aureka\VBBundle\Exception\VBUserException;


class LoginHandler implements AuthenticationSuccessHandlerInterface
{

    private $userProvider;
    private $session;

    private $userToLogIn;


    public function __construct(VBUsers $user_provider, VBSession $session)
    {
        $this->userProvider = $user_provider;
        $this->session = $session;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();
        try {
            $this->userToLogIn = $this->userProvider->load($this->session, $user->getUsername());
        } catch (VBUserException $e) {
            $this->userToLogIn = $this->userProvider->create($this->session, $user->getUsername(), $user->getPassword());
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
