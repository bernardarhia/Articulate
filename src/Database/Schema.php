<?php

namespace App\Database;


use stdClass;
// use App\Database\AlterTable;
trait AlterTable
{
    static private $connection;
    static public $tableName;
    static protected $statement = null;

    //CHECKERS
    static private $rename = false;
    static private $createDb = false;
    static private $create = false;
    static protected $db = null;
    public static function table($tableName, $callback)
    {
        self::$tableName = $tableName;
        // $class  = new AlterTable;
        $callback(new self);
        return new static;
    }

    public function dropColumn($column)
    {

        if (gettype($column) == "string") {
            $tableName = $this->tableName;
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
        $tableName = $this->tableName;
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
    public static function truncate()
    {
        $tableName = self::$tableName;
        self::$statement = "TRUNCATE TABLE $tableName";
        return new static;
    }
    public static function hasColumn($column)
    {
        $tableName = self::$tableName;
        self::$statement = "SHOW COLUMNS FROM $tableName LIKE '$column'";
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
}

class Schema extends Table
{
    use AlterTable;
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
        return new static;
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

    public $incrementcolumn = null;
    public bool $timestamps =  true;
    private $itemIndex = null;
    private $is_null = "NOT NULL";
    private $schema = [];

    static function connection(string $db)
    {
        self::$statement = "CREATE DATABASE IF NOT EXISTS $db; USE $db;";
        self::$db = $db;
        return new static;
    }

    // ==============THE DATA TYPES OF THE DATABASE==============


    public function increment($column, $length = 11)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "INT($length) AUTO_INCREMENT";
        return $this;
    }
    public function bigIncrements($column, $length = 11)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "BIGINT($length) AUTO_INCREMENT";
        return $this;
    }


    public function integer($column, $length = 11)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "INT($length)";
        return $this;
    }
    public function double($column, $digits, $decimalPoints)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "DOUBLE($digits, $decimalPoints)";
        return $this;
    }

    public function primaryKey()
    {
        $this->schema[$this->itemIndex] .= " PRIMARY KEY";
        return $this;
    }

    public function string($column, $length = 100)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "VARCHAR($length)";
        return $this;
    }
    public function text($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "TEXT";
        return $this;
    }
    public function mediumText($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "MEDIUMTEXT";
        return $this;
    }
    public function longText($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "LONGTEXT";
        return $this;
    }
    public function bigInteger($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "BIGINT";
        return $this;
    }
    public function mediumInteger($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "MEDIUMINT";
        return $this;
    }
    public function smallInteger($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "SMALLINT";
        return $this;
    }
    public function tinyInteger($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "TINYINT";
        return $this;
    }

    public function float($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "FLOAT";
        return $this;
    }
    public function binary($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "BINARY";
        return $this;
    }
    public function boolean($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "BOOLEAN";
        return $this;
    }
    public function date($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "DATE";
        return $this;
    }
    public function datetime($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "DATETIME";
        return $this;
    }
    public function time($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "TIME";
        return $this;
    }

    public function json($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "JSON";
        return $this;
    }
    public function jsonb($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "JSONB";
        return $this;
    }

    public function char($column, $length = 4)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "CHAR($length)";
        return $this;
    }

    public function enum($column, $args)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] .= " ENUM(" .  implode(", ", $args) . ")";
        return $this;
    }

    // ==============END OF THE DATA TYPES OF THE DATABASE==============


    // ==============THESE ARE THE CONSTRAINTS OF THE DATABASE TABLE==============
    public function nullable()
    {
        $this->is_null = "NULL";
        $this->schema[$this->itemIndex] .= " $this->is_null";
        return $this;
    }
    public function unique()
    {
        $this->schema[$this->itemIndex] .= " UNIQUE";
        return $this;
    }

    public function signed()
    {
        if (strpos($this->schema[$this->itemIndex], "UNSIGNED") !== false) {
            $this->schema[$this->itemIndex] = str_replace("UNSIGNED", "SIGNED", $this->schema[$this->itemIndex]);
        } else
            $this->schema[$this->itemIndex] .= " SIGNED ";
        return $this;
    }
    public function unsigned()
    {
        if (strpos($this->schema[$this->itemIndex], "SIGNED") !== false) {
            $this->schema[$this->itemIndex] = str_replace("SIGNED", "UNSIGNED", $this->schema[$this->itemIndex]);
        } else
            $this->schema[$this->itemIndex] .= " UNSIGNED ";
        return $this;
    }

    public function foreign()
    {
        $this->schema[$this->itemIndex] .= " FOREIGN KEY";
        return $this;
    }
    public function references($table, $column)
    {
        $this->schema[$this->itemIndex] .= " REFERENCES $table($column)";
        return $this;
    }
    // ==============END OF THE CONSTRAINTS OF THE DATABASE TABLE==============


    public function timestamps()
    {
        $this->timestamps = true;
        $this->schema["created_at"] = "DATETIME DEFAULT CURRENT_TIMESTAMP";
        $this->schema["updated_at"] = "DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        return $this;
    }

    public function softDeletes()
    {
        $this->schema["deleted_at"] = "DATETIME DEFAULT NULL";
        return $this;
    }
    public function timestamp($column)
    {
        $this->schema[$column] = "DATETIME DEFAULT CURRENT_TIMESTAMP";
        return $this;
    }

    public function default($column)
    {
        $this->schema[$this->itemIndex] .= " DEFAULT('$column')";
        return $this;
    }

    public function rememberToken()
    {
        $this->schema["remember_token"] = "VARCHAR(100) DEFAULT NULL";
        return $this;
    }

    public function getSchema()
    {
        return $this->schema;
    }
}
// Schema::table("users", function ($table) {
// });
Schema::table("users", function ($table) {
    $table->dropColumn(["views", "names", "email", "password", "remember_token"]);
});