<?php

namespace Aureka\VBBundle\Factory;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

use Aureka\VBBundle\VBConfiguration;
use Aureka\VBBundle\Event\Listener\LoginListener;
use Aureka\VBBundle\Handler\LogoutHandler;
use Aureka\VBBundle\VBUsers;


class ServicesFactory
{

    public static function createLogin(VBConfiguration $config, RequestStack $request_stack)
    {
        $user_provider = new VBUsers($config->createDB());
        $request = $request_stack->getMasterRequest() ?: new Request();
        return new LoginListener($user_provider, $config->createSession($request));
    }

    public static function createLogout(VBConfiguration $config, RequestStack $request_stack)
    {
        $user_provider = new VBUsers($config->createDB());
        $request = $request_stack->getMasterRequest() ?: new Request();
        return new LogoutHandler($user_provider, $config->createSession($request));
    }
}