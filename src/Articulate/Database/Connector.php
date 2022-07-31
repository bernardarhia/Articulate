<?php

namespace Articulate\Database;

use PDO as DBConnector;


class Connector extends DBConnector
{
    protected static $connection;
    public function __construct($engine = null, string $dbName = null, string $dbHost = null, string $dbUser = null, string $dbPass = '', $options = null)
    {

        $this->engine = $engine;
        $this->dbName = $dbName;
        $this->dbHost = $dbHost;
        $this->dbUser = $dbUser;
        $this->dbPass = $dbPass;
        try {
            //code...
            parent::__construct("$this->engine:host=$this->dbHost;dbname=$this->dbName", $this->dbUser, $this->dbPass);
            static::$connection = new DBConnector("$this->engine:host=$this->dbHost;dbname=$this->dbName", $this->dbUser, $this->dbPass);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}