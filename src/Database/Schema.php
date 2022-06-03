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


class Table
{
    static protected $db = null;
    static protected $statement = null;
    public $incrementValue = null;
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
    public function increment($value, $length = 11)
    {
        $this->itemIndex = $value;
        $this->schema[$this->itemIndex] = "INT($length) AUTO_INCREMENT";
        return $this;
    }

    public function string($value, $length = 100)
    {
        $this->itemIndex = $value;
        $this->schema[$this->itemIndex] = "VARCHAR($length)";
        return $this;
    }
    public function integer($value, $length = 11)
    {
        $this->itemIndex = $value;
        $this->schema[$this->itemIndex] = "INT($length)";
        return $this;
    }

    public function primaryKey()
    {
        $this->schema[$this->itemIndex] .= " PRIMARY KEY";
        return $this;
    }

    public function enum($value, $args)
    {
        $this->itemIndex = $value;
        $this->schema[$this->itemIndex] .= " ENUM(" .  implode(", ", $args) . ")";
        return $this;
    }

    public function text($value)
    {
        $this->itemIndex = $value;
        $this->schema[$this->itemIndex] = "TEXT";
        return $this;
    }
    public function bigInteger($value, $length = 11)
    {
        $this->itemIndex = $value;
        $this->schema[$this->itemIndex] = "BIGINT($length)";
        return $this;
    }
    public function float($value)
    {
        $this->itemIndex = $value;
        $this->schema[$this->itemIndex] = "FLOAT";
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
    public function timestamp($value)
    {
        $this->schema[$value] = "DATETIME DEFAULT CURRENT_TIMESTAMP";
        return $this;
    }

    // public function dropColumn($column)
    // {
    //     self::$statement = "ALTER TABLE `$this->itemIndex` DROP COLUMN `$column`";
    //     return new static;
    // }

    // public function dropForeignKey($column)
    // {
    //     self::$statement = "ALTER TABLE `$this->itemIndex` DROP FOREIGN KEY `$column`";
    //     return new static;
    // }

    // public function dropPrimaryKey()
    // {
    //     self::$statement = "ALTER TABLE `$this->itemIndex` DROP PRIMARY KEY";
    //     return new static;
    // }

    // public function dropUniqueKey($column)
    // {
    //     self::$statement = "ALTER TABLE `$this->itemIndex` DROP UNIQUE `$column`";
    //     return new static;
    // }

    // public function dropTimestamps()
    // {
    //     self::$statement = "ALTER TABLE `$this->itemIndex` DROP COLUMN `created_at`";
    //     self::$statement .= ", DROP COLUMN `updated_at`";
    //     return new static;
    // }

    // public function dropSoftDeletes()
    // {
    //     self::$statement = "ALTER TABLE `$this->itemIndex` DROP COLUMN `deleted_at`";
    //     return new static;
    // }



    public function default($value)
    {
        $this->schema[$this->itemIndex] .= " DEFAULT('$value')";
        return $this;
    }
    public function getSchema()
    {
        return $this->schema;
    }
}
Schema::create("user", function (Table $table) {
    $table->integer("id")->primaryKey()->signed()->unsigned();
    $table->string("email", 20);
    $table->integer("phone", 12)->unique();
    $table->foreign('user_id')->references('account', 'id');
    $table->bigInteger('votes');
    $table->float('rating');
    $table->text('comment');
    print_r($table->getSchema());
});