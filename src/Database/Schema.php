<?php

namespace App\Database;

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
    public static function drop($tableName)
    {
        self::$statement = "DROP TABLE IF EXISTS $tableName";
        return new static;
    }
    public static function h()
    {
        print_r(__DIR__);
    }
}

Schema::create("user", function (Table $table) {
    $table->increment("id")->primaryKey()->unique();
    $table->string("email", 20);
    $table->int("phone", 12)->unique();
    print_r($table->getSchema());
});