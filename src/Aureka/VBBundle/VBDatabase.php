<?php

namespace Aureka\VBBundle;

use Doctrine\DBAL\Connection,
    Doctrine\DBAL\Configuration,
    Doctrine\DBAL\DriverManager;


class VBDatabase
{

    private $dbParams;
    private $tablePrefix;
    private $connection;


    public function __construct(array $db_params, $table_prefix = '')
    {
        $this->dbParams =$db_params;
        $this->tablePrefix = $table_prefix;
    }


    public function connect()
    {
        if (is_null($this->connection)) {
            $config = new Configuration();
            $this->connection = DriverManager::getConnection($this->dbParams, $config);
        }
        return $this;
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
        $query = sprintf('SELECT * FROM %s WHERE username = ?', $this->prefix($table), $this->prepareFilters($filters));
        return $this->connection->fetchAssoc($query, array_values($filters));
    }


    private function prefix($table)
    {
        return "$this->tablePrefix$table";
    }


    private function prepareFilters($filters)
    {
        return implode(' ', array_map(function ($key) { return "$key = ?"; }, array_keys($filters)));
    }
}