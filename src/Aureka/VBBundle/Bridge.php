<?php

namespace Aureka\VBBundle;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RequestStack;

use Doctrine\DBAL\Connection,
    Doctrine\DBAL\Configuration,
    Doctrine\DBAL\DriverManager;


class Bridge
{

    private $request;
    private $connection;


    public function __construct(Request $request, Connection $connection = null)
    {
        $this->request = $request;
        $this->connection = $connection;
    }


    public static function createForDB(RequestStack $request_stack, array $db_params)
    {
        $config = new Configuration();
        $conn = DriverManager::getConnection($db_params, $config);
        return new static($request_stack->getCurrentRequest(), $conn);
    }


    public function createUser($username)
    {

    }


    public function loadUser($username)
    {

    }


    public function login($username)
    {
    }
}
