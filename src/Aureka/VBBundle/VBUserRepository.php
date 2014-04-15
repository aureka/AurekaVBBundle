<?php

namespace Aureka\VBBundle;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RequestStack;

use Doctrine\DBAL\Connection,
    Doctrine\DBAL\Configuration,
    Doctrine\DBAL\DriverManager;


class VBUserRepository
{

    private $request;
    private $connection;
    private $tablePrefix;


    public function __construct(Request $request, Connection $connection, $table_prefix = '')
    {
        $this->request = $request;
        $this->connection = $connection;
        $this->tablePrefix = $table_prefix;
    }


    public static function createForDB(RequestStack $request_stack, array $db_params)
    {
        $config = new Configuration();
        $conn = DriverManager::getConnection($db_params, $config);
        return new static($request_stack->getCurrentRequest(), $conn);
    }


    public function create($username)
    {
        $this->connection->insert($this->tableName('user'), array('username' => $username));
        return $this;
    }


    public function load($username)
    {

    }


    public function login($username)
    {
    }


    private function tableName($table)
    {
        return "$this->tablePrefix$table";
    }
}
