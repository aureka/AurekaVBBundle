<?php

namespace Aureka\VBBundle\Factory;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

use Aureka\VBBundle\VBConfiguration;
use Aureka\VBBundle\Event\Listener\LoginListener;
use Aureka\VBBundle\VBUsers;


class ServicesFactory
{

    public static function createLogin(VBConfiguration $config, RequestStack $request_stack)
    {
        return self::create('Aureka\VBBundle\Handler\LoginHandler', $config, $request_stack);
    }


    public static function createLogout(VBConfiguration $config, RequestStack $request_stack)
    {
        return self::create('Aureka\VBBundle\Handler\LogoutHandler', $config, $request_stack);
    }


    private static function create($class_name, VBConfiguration $config, RequestStack $request_stack)
    {

        $user_provider = new VBUsers($config->createDB());
        $request = $request_stack->getMasterRequest() ?: new Request();
        return new $class_name($user_provider, $config->createSession($request));
    }
}
