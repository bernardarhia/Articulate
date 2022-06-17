<?php

namespace Articulate\Database;

use Articulate\Dotenv;
use PDO as DBConnector;

include_once __DIR__ . "/../Dotenv.php";

class Connector extends DBConnector
{
    private $connection;
    public function __construct(string $dbName = null, string $dbHost = null, string $dbUser = null, string $dbPass = '')
    {
        $env = new Dotenv($_SERVER['DOCUMENT_ROOT'] . "/.env");
        $env->load();
        $this->dbName = $dbName;
        $this->dbName = !$dbName || is_null($dbName) ? $_ENV['DB_NAME'] : $dbName;
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