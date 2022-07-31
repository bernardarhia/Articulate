<?php

namespace Articulate\Database;

use Articulate\Commands\Execute;
use Articulate\Commands\KEYWORDS;
use Articulate\Table\Table;

class Schema extends Table
{
    static private $Schema;
    static $statement;
    static protected $table;
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

        self::$Schema = $table->schema;
        return self::save();
    }

    /**
     * 
     * @param $from The current database table name you want to change
     * @param $to The name of the new table you want to change the name to
     */
    public static function rename(string $from, string $to)
    {
        self::$statement = KEYWORDS::RENAME . " TABLE `$from` TO `$to`";
        return self::save();
    }
    /**
     * @param $db Name of the database you want to create
     * 
     */
    public static function createDatabase(string $db)
    {
        self::$statement = KEYWORDS::CREATE . " DATABASE IF NOT EXISTS $db";
        return self::save();
    }

    /**
     * @param $db Name of the database you want to drop
     * 
     */
    public static function dropDatabase(string $db)
    {
        // self::$statement = self::DROP . " DATABASE IF EXISTS $db";
        self::$statement = KEYWORDS::DROP . " DATABASE  $db";
        return self::save();
    }
    public function schemaCollect()
    {
        return self::$Schema;
    }

    public static function save()
    {
        $result = new \stdClass;
        $result->executed = false;
        $result->errorInfo = (object)[
            "errorCode" => null,
            "errorMessage" => null,
        ];



        self::$statement = "CREATE TABLE IF NOT EXISTS " . self::$table . " (\n";
        foreach (self::$Schema as $key => $value) {
            if (strpos($value, "NULL") === false)
                self::$statement .= $key . " " . trim($value) . " " . self::$is_null  . ",\n";
            else
                self::$statement .= $key . " " . trim($value) . ",\n";
        }
        self::$statement = rtrim(self::$statement, ",\n");
        self::$statement .= "\n)";
        $stmt = self::$connection->query(self::$statement);
        if ($stmt) {
            $result->executed = true;
        } else {
            $result->errorInfo = self::$connection->errorInfo();
        }
        return $result;
    }
    static function reset()
    {
        self::$statement = null;
    }
}