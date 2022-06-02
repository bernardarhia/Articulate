<?php

namespace App\Database;

use PDO;
// use PDO as DBConnector;

class Connector extends PDO
{
    private $connection;
    public function __construct($dbName, $dbHost = 'localhost', $dbUser = 'root', $dbPass = '')
    {
        $this->dbName = $dbName;
        // parent::__construct("mysql:host=$dbHost;dbname=$this->dbName", $dbUser, $dbPass);
        parent::__construct("mysql:host=$dbHost;dbname=$this->dbName", $dbUser, $dbPass);
        if (is_null($this->dbName))
            $this->connection = new PDO("mysql:host=$dbHost", $dbUser, $dbPass, null);
        else
            $this->connection = new PDO("mysql:host=$dbHost;dbname=$this->dbName", $dbUser, $dbPass, null);
        return $this->connection;
    }

    public function c()
    {
        print_r($this->dbName);
    }
}