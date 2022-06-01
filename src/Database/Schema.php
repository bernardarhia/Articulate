<?php

namespace App;

use PDO;

class Schema extends Table
{
    static protected $table;

    /**
     * @param string $tableName 
     * The name of the database table you want to create
     * @param callable $callback A callback function to define the properties of the table you want to create
     * 
     */
    static function create(string $tableName, callable $callback)
    {
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );
        self::$table = $tableName;
        $table = new Table("mysql:host=localhost;dbname=" . self::$db, "root", "", $options);
        $callback($table);
        return new static(self::$db);
    }

    public function save()
    {
        print_r(self::$table);
    }

    /**
     * 
     * @param $from The current database table name you want to change
     * @param $to The name of the new table you want to change the name to
     */
    public function rename(string $from, string $to)
    {
        $statement = "RENAME IF EXISTS `$from` TO `$to`";
    }
}

class Table extends PDO
{
    static protected $db;
    public $incrementValue = null;
    public function increment($value, $length = 100)
    {
        $this->incrementValue = $value;
    }

    public function string($value, $length = 100)
    {
        return $this;
    }
    static function connection(string $db)
    {
        self::$db = $db;
        return new static($db);
    }
}


Schema::connection("users")->save();