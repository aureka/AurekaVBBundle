<?php

namespace Aureka\VBBundle;

class VBConfiguration
{

    public $defaultUserGroup;
    public $cookiePrefix;
    public $dbDriver;
    public $dbHost;
    public $dbName;
    public $dbPort;
    public $dbUser;
    public $dbPassword;
    public $dbPrefix;

    public function __construct(
        $default_user_group,
        $cookie_prefix,
        $ip_check,
        $db_driver,
        $db_host,
        $db_name,
        $db_port,
        $db_user,
        $db_password,
        $db_prefix)
    {
        $this->defaultUserGroup = $default_user_group;
        $this->ipCheck = $ip_check;
        $this->cookiePrefix = $cookie_prefix;
        $this->dbDriver = $db_driver;
        $this->dbHost = $db_host;
        $this->dbName = $db_name;
        $this->dbPort = $db_port;
        $this->dbUser = $db_user;
        $this->dbPassword = $db_password;
        $this->dbPrefix = $db_prefix;
    }


    public function createDB()
    {
        $db_params = array(
            'driver' => $this->dbDriver,
            'host' => $this->dbHost,
            'port' => $this->dbPort,
            'dbname' => $this->dbName,
            'user' => $this->dbUser,
            'password' => $this->dbPassword,
        );
        return new VBDatabase($db_params, $this->dbPrefix);
    }


}
