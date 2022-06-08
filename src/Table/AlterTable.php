<?php

namespace App\Table;

use App\Command\Command;

include_once __DIR__ . "/../Commands/Commands.php";

class AlterTable extends Command
{
    static protected $connection;
    static public $tableName;
    static protected $statement = null;
    public $engine;

    //CHECKERS
    static protected $rename = false;
    static protected $createDb = false;
    static protected $dropDatabase = false;
    static protected $create = false;
    static protected $alter = false;
    static protected $db = null;
    public static function table($tableName, $callback)
    {
        self::$alter = true;
        self::$tableName = $tableName;
        $callback(new self);
        return new static;
    }

    public function dropColumn($column)
    {

        if (gettype($column) == "string") {
            $tableName = self::$tableName;
            // alter table if not exists $tableName drop column $column;

            self::$statement = self::ALTER . " TABLE `$tableName` " . self::DROP . " COLUMN `$column`";
        } else if (gettype($column) == "array") {
            $tableName = self::$tableName;
            $columns = implode("," . self::DROP . " COLUMN ", $column);
            self::$statement = self::ALTER . " TABLE `$tableName` " . self::DROP . " COLUMN $columns";
        }
        return new static;
    }

    public function dropForeignKey($column)
    {
        $tableName = $this->tableName;
        self::$statement = self::ALTER . " TABLE `$tableName` " . self::DROP . " FOREIGN KEY `$column`";
        return new static;
    }

    public  function dropPrimaryKey()
    {
        $tableName = $this->tableName;
        self::$statement = self::ALTER . " TABLE `$tableName` " . self::DROP . " PRIMARY KEY";
        return new static;
    }

    public function dropUniqueKey($column)
    {
        $tableName = $this->tableName;
        self::$statement = self::ALTER . " TABLE `$tableName` " . self::DROP . " UNIQUE `$column`";
        return new static;
    }

    public function dropTimestamps()
    {
        $tableName = self::$tableName;
        self::$statement = self::ALTER . " TABLE `$tableName` " . self::DROP . "  COLUMN `created_at`";
        self::$statement .= ", DROP COLUMN `updated_at`";
        return new static;
    }

    public function dropSoftDeletes()
    {
        $tableName = $this->tableName;
        self::$statement = self::ALTER . " TABLE `$tableName` " . self::DROP . " COLUMN `deleted_at`";
        return new static;
    }

    public static function renameColumn($oldColumn, $newColumn)
    {
        $tableName = self::$tableName;
        self::$statement = self::ALTER . " TABLE `$tableName` CHANGE `$oldColumn` `$newColumn`";
        return new static;
    }
    public static function drop($tableName)
    {
        self::$statement = self::DROP . " TABLE $tableName";
    }
    public static function dropIfExists($tableName)
    {
        self::$statement = self::DROP . " TABLE IF EXISTS $tableName";
        return new static;
    }
    public function truncate()
    {

        self::$statement = self::TRUNCATE . " TABLE " . self::$tableName;
        return new static;
    }
    public function hasColumn($column)
    {
        $tableName = self::$tableName;
        self::$statement = "SHOW COLUMNS FROM $tableName LIKE '$column'";
        return new static;
    }
}