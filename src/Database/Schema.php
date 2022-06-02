<?php

namespace App;

use PDO;
use stdClass;

class Schema extends Table
{
    static protected $table;
    static private $connection;

    //CHECKERS
    static private $rename = false;
    static private $createDb = false;
    static private $create = false;
    /**
     * @param string $tableName 
     * The name of the database table you want to create
     * @param callable $callback A callback function to define the properties of the table you want to create
     * 
     */
    static function create(string $tableName, callable $callback)
    {

        self::$table = $tableName;
        $table = new Table;
        $callback($table);
        self::$create = true;
        return new static;
    }

    public function save()
    {
        $result = new stdClass;
        self::$connection = new Connector(self::$db);
        if (self::$rename || self::$createDb) {
            $stmt = self::$connection->prepare(self::$statement);
            $result->executed =  $stmt->execute();
        }
        self::$statement = null;
        self::$rename = false;
        return $result;
    }

    /**
     * 
     * @param $from The current database table name you want to change
     * @param $to The name of the new table you want to change the name to
     */
    public function rename(string $from, string $to)
    {
        self::$statement = "RENAME TABLE `$from` TO `$to`";
        self::$rename = true;
        return new static;
    }
    /**
     * @param $db Name of the database you want to create
     * 
     */
    public static function createDatabase(string $db)
    {
        self::$statement = "CREATE DATABASE IF NOT EXISTS $db";
        self::$createDb = true;
        return new static;
    }

    /**
     * @param $db Name of the database you want to drop
     * 
     */
    public static function dropDatabase(string $db)
    {
        self::$statement = "DROP DATABASE IF EXISTS $db";
        self::$createDb = true;
        return new static;
    }
}

class Table
{
    static protected $db = null;
    static protected $statement = null;
    public $incrementValue = null;
    public bool $timestamps =  true;
    private $schema = [];



    public function increment($value, $length = 11)
    {
        $this->schema[] = "`$value` INT( $length ) NOT NULL AUTO_INCREMENT";
        $this->incrementValue = $value;
        return $this;
    }

    public function string($value, $length = 100)
    {
        return $this;
    }
    static function connection(string $db)
    {
        self::$statement = "CREATE DATABASE IF NOT EXISTS $db; USE $db;";
        self::$db = $db;
        return new static;
    }
    public function primaryKey()
    {
        $this->schema[] = "PRIMARY KEY (`$this->incrementValue`)";
        return $this;
    }

    public function getSchema()
    {
        return $this->schema;
    }
}



/**
 * 
 * 
 */
class Connector extends PDO
{
    private $connection;
    public function __construct(string $dbName, string $dbHost = 'localhost', string $dbUser = 'root', string $dbPass = '')
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
}

Schema::create("user", function ($table) {
    $table->string("name")->nullable();
});