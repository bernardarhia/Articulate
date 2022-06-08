<?php

namespace App\Table;

class AlterTable
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

            self::$statement = "ALTER TABLE `$tableName` DROP COLUMN `$column`";
        } else if (gettype($column) == "array") {
            $tableName = self::$tableName;
            $columns = implode(",DROP COLUMN ", $column);
            self::$statement = "ALTER TABLE `$tableName` DROP COLUMN $columns";
        }
        return new static;
    }

    public function dropForeignKey($column)
    {
        $tableName = $this->tableName;
        self::$statement = "ALTER TABLE `$tableName` DROP FOREIGN KEY `$column`";
        return new static;
    }

    public function dropPrimaryKey()
    {
        $tableName = $this->tableName;
        self::$statement = "ALTER TABLE `$tableName` DROP PRIMARY KEY";
        return new static;
    }

    public function dropUniqueKey($column)
    {
        $tableName = $this->tableName;
        self::$statement = "ALTER TABLE `$tableName` DROP UNIQUE `$column`";
        return new static;
    }

    public function dropTimestamps()
    {
        $tableName = self::$tableName;
        self::$statement = "ALTER TABLE `$tableName` DROP COLUMN `created_at`";
        self::$statement .= ", DROP COLUMN `updated_at`";
        return new static;
    }

    public function dropSoftDeletes()
    {
        $tableName = $this->tableName;
        self::$statement = "ALTER TABLE `$tableName` DROP COLUMN `deleted_at`";
        return new static;
    }

    public static function renameColumn($oldColumn, $newColumn)
    {
        $tableName = self::$tableName;
        self::$statement = "ALTER TABLE `$tableName` CHANGE `$oldColumn` `$newColumn`";
        return new static;
    }
    public static function drop($tableName)
    {
        self::$statement = "DROP TABLE $tableName";
        return new static;
    }
    public static function dropIfExists($tableName)
    {
        self::$statement = "DROP TABLE IF EXISTS $tableName";
        return new static;
    }
    public function truncate()
    {

        self::$statement = "TRUNCATE TABLE " . self::$tableName;
        return new static;
    }
    public function hasColumn($column)
    {
        $tableName = self::$tableName;
        self::$statement = "SHOW COLUMNS FROM $tableName LIKE '$column'";
        return new static;
    }
}