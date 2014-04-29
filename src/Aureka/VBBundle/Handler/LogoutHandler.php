<?php

namespace Aureka\VBBundle\Handler;

use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Aureka\VBBundle\VBUsers,
    Aureka\VBBundle\VBSession;

class LogoutHandler implements LogoutHandlerInterface
{

    private $userProvider;
    private $session;


    public function __construct(VBUsers $user_provider, VBSession $session)
    {
        $this->userProvider = $user_provider;
        $this->session = $session;
    }


    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        $this->session->logout($response);
        $this->userProvider->removeSession($this->session);
        return $this;
    }
}
