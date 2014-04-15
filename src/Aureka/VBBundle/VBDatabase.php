<?php

namespace Aureka\VBBundle;

use Doctrine\DBAL\Connection,
    Doctrine\DBAL\Configuration,
    Doctrine\DBAL\DriverManager;


class VBDatabase
{

    private $connection;
    private $tablePrefix;


    public function __construct(Connection $connection, $table_prefix = '')
    {
        $this->request = $request;
        $this->connection = $connection;
        $this->tablePrefix = $table_prefix;
    }


    public static function create(array $db_params, $table_prefix)
    {
        $config = new Configuration();
        $conn = DriverManager::getConnection($db_params, $config);
        return new static($conn, $table_prefix);
    }


    public function insert($table, $fields)
    {
        return $this->connection->insert($this->prefix($table), $fields);
    }


    public function delete($table, $filters)
    {
        return $this->connection->delete($this->prefix($table), $filters);
    }


    public function load($table, $filters)
    {
        $query = sprintf('SELECT * FROM %s WHERE username = ?', $table, $this->prepareFilters($filters));
        return $this->connection->fetchAssoc($table, array_values($filters));
    }


    private function prefix($table)
    {
        return "$this->tablePrefix$table";
    }


    private function prepareFilters($table, $filters)
    {
        return implode(' ', array_map(function ($key) { return "$key = ?"; }, array_keys($filters)));
    }
}