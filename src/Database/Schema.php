<?php

namespace App\Database;

use App\Table\Table;

include_once __DIR__ . "/../Table/Table.php";
include_once __DIR__ . "/../Database/Connector.php";
class Schema extends Table
{
    static private $Schema;

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
        self::$create = true;

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
        self::$statement = "RENAME TABLE `$from` TO `$to`";
        self::$rename = true;
        return self::save();
    }
    /**
     * @param $db Name of the database you want to create
     * 
     */
    public static function createDatabase(string $db)
    {
        self::$statement = "CREATE DATABASE IF NOT EXISTS $db";
        self::$createDb = true;
        return self::save();
    }

    /**
     * @param $db Name of the database you want to drop
     * 
     */
    public static function dropDatabase(string $db)
    {
        self::$dropDatabase = true;
        self::$statement = "DROP DATABASE IF EXISTS $db";
        self::$createDb = true;
        return self::save();
    }
    public function schemaCollect()
    {
        return self::$Schema;
    }

    public static function save()
    {
        $result = new \stdClass;
        self::$connection = new Connector(self::$db);
        if (self::$rename || self::$createDb || self::$dropDatabase || self::$alter) {
            $stmt = self::$connection->prepare(self::$statement);
            $result->executed =  $stmt->execute();
            self::reset();
        }

        if (self::$create) {
            $old_statement = self::$statement;
            self::$statement = "";
            // echo self::$statement;
            foreach (self::$Schema as $key => $value) {
                if (strpos($value, "NULL") === false)
                    self::$statement .= $key . " " . trim($value) . " " . self::$is_null  . ",\n";
                else
                    self::$statement .= $key . " " . trim($value) . ",\n";
            }
            self::$statement =  $old_statement . "CREATE TABLE IF NOT EXISTS `" . self::$table . "`(" . substr(self::$statement, 0, -2) . ")";
            $stmt = self::$connection->prepare(self::$statement);
            $result->executed =  $stmt->execute();
            self::reset();
        }

        return $result;
    }
    static private function reset()
    {
        self::$statement = null;
        self::$rename = false;
    }
}
// $renamed = Schema::rename("users", "users1");
$result = Schema::create("acc", function ($table) {
    $table->increment("id")->primaryKey();
    $table->string("email");
    // print_r($table);
});
print_r($result);