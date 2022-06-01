<?php

class Schema extends Table
{
    static $table;

    /**
     * @param string $tableName 
     * The name of the database table you want to create
     * @param callable $callback A callback function to define the properties of the table you want to create
     * 
     */
    static function create(string $tableName, callable $callback)
    {
        self::$table = $tableName;
        $callback(new Table);
        return new static;
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

class Table
{
    public $incrementValue = null;
    public function increment($value, $length = 100)
    {
        $this->incrementValue = $value;
    }
}


Schema::create("users", function ($table) {
    $table->increment("id");
    $table->string("name");
})->save();