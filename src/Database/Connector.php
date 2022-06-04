<?php

namespace App\Database;

use App\Dotenv;
use PDO as DBConnector;

include_once __DIR__ . "/../Dotenv.php";

class Connector extends DBConnector
{
    private $connection;
    public function __construct(string $dbName = null, string $dbHost = 'localhost', string $dbUser = 'root', string $dbPass = '')
    {
        $env = new Dotenv(__DIR__ . "/../../.env");
        $env->load();

        $this->dbName = $dbName;
        $this->dbHost = $dbHost ?? $_ENV['DB_HOST'];
        $this->dbUser = $dbUser ?? $_ENV['DB_USER'];
        $this->dbPass = $dbPass ?? $_ENV['DB_PASS'];
        $this->engine = $dbEngine ?? $_ENV['DB_DIALECT'];
        try {
            //code...
            if ($this->dbName == null) {
                parent::__construct("$this->engine:host=$this->dbHost", $this->dbUser, $this->dbPass);
                $this->connection = new DBConnector("$this->engine:host=$this->dbHost", $this->dbUser, $this->dbPass);
            } else {
                $this->dbName = $_ENV['DB_NAME'];
                parent::__construct("$this->engine:host=$this->dbHost;dbname=$this->dbName", $this->dbUser, $this->dbPass);
                $this->connection = new DBConnector("$this->engine:host=$this->dbHost;dbname=$this->dbName", $this->dbUser, $this->dbPass);
            }
            return $this->connection;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}