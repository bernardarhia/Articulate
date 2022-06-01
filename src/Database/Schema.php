<?php

class Schema extends Table
{
    static $table;
    static function create($tableName, $callback)
    {
        self::$table = $tableName;
        $callback(new Table);
        return new static;
    }

    public function save()
    {
        print_r(self::$table);
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