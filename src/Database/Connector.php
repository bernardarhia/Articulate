<?php

namespace App\Database;

use App\Dotenv;
use PDO as DBConnector;


class Connector extends DBConnector
{
    private $connection;
    public function __construct(string $dbName, string $dbHost = 'localhost', string $dbUser = 'root', string $dbPass = '')
    {
        // $env = new Dotenv(__DIR__ . "/../../.env");
        // $env->load();
        // echo $_ENV['DB_NAME'];

        $this->dbName = $dbName;
        // parent::__construct("mysql:host=$dbHost;dbname=$this->dbName", $dbUser, $dbPass);
        parent::__construct("mysql:host=$dbHost;dbname=$this->dbName", $dbUser, $dbPass);
        if (is_null($this->dbName))
            $this->connection = new DBConnector("mysql:host=$dbHost", $dbUser, $dbPass, null);
        else
            $this->connection = new DBConnector("mysql:host=$dbHost;dbname=$this->dbName", $dbUser, $dbPass, null);
        return $this->connection;
    }
}
$connect = new Connector("altisend");