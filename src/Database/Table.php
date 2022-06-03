<?php

namespace App\Database;

class Table
{
    static protected $db = null;
    static protected $statement = null;
    public $incrementValue = null;
    public bool $timestamps =  true;
    private $itemIndex = null;
    private $is_null = "NOT NULL";
    private $schema = [];

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
    static function connection(string $db)
    {
        self::$statement = "CREATE DATABASE IF NOT EXISTS $db; USE $db;";
        self::$db = $db;
        return new static;
    }
    public function primaryKey()
    {
        $this->schema[$this->itemIndex] .= " PRIMARY KEY";
        return $this;
    }

    public function nullable()
    {
        $this->is_null = "NULL";
        $this->schema[$this->itemIndex] .= " $this->is_null";
        return $this;
    }
    public function enum($value, $args)
    {
        $this->itemIndex = $value;
        $this->schema[$this->itemIndex] .= " ENUM(" .  implode(", ", $args) . ")";
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

    public function getSchema()
    {
        return $this->schema;
    }
}