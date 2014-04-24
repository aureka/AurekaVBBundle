<?php

namespace Aureka\VBBundle\Handler;

use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

use Aureka\VBBundle\VBUsers,
    Aureka\VBBundle\VBConfiguration,
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


    public static function createFor(VBConfiguration $config, RequestStack $request_stack)
    {
        $user_provider = new VBUsers($config->createDB());
        $request = $request_stack->getMasterRequest() ?: new Request();
        return new static($user_provider, $config->createSession($request));
    }


    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        $this->userProvider->removeSession($this->session);
        $this->session->logout($response);
        return $this;
    }
}
