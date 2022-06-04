<?php

namespace App\Table;

use App\Table\AlterTable;

include_once __DIR__ . "/../Table/AlterTable.php";
class Table extends AlterTable
{
    // public bool $timestamps =  true;
    private $itemIndex = null;
    protected $is_null = "NOT NULL";
    protected $schema = [];

    static function connection(string $db)
    {
        self::$statement = "CREATE DATABASE IF NOT EXISTS $db;USE $db;";
        self::$db = $db;
        return new static;
    }

    // ==============THE DATA TYPES OF THE DATABASE==============


    public function increment($column, $length = 11)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "INT($length) AUTO_INCREMENT ";
        return $this;
    }
    public function bigIncrements($column, $length = 11)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "BIGINT($length) AUTO_INCREMENT ";
        return $this;
    }


    public function integer($column, $length = 11)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "INT($length) ";
        return $this;
    }
    public function double($column, $digits, $decimalPoints)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "DOUBLE($digits, $decimalPoints) ";
        return $this;
    }

    public function primary()
    {
        $this->schema[$this->itemIndex] .= "PRIMARY KEY ";
        return $this;
    }

    public function string($column, $length = 100)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "VARCHAR($length) ";
        return $this;
    }
    public function text($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "TEXT ";
        return $this;
    }
    public function mediumText($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "MEDIUMTEXT ";
        return $this;
    }
    public function longText($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "LONGTEXT ";
        return $this;
    }
    public function bigInteger($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "BIGINT ";
        return $this;
    }
    public function mediumInteger($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "MEDIUMINT ";
        return $this;
    }
    public function smallInteger($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "SMALLINT ";
        return $this;
    }
    public function tinyInteger($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "TINYINT ";
        return $this;
    }

    public function float($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "FLOAT ";
        return $this;
    }
    public function binary($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "BINARY ";
        return $this;
    }
    public function boolean($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "BOOLEAN ";
        return $this;
    }
    public function date($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "DATE ";
        return $this;
    }
    public function datetime($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "DATETIME ";
        return $this;
    }
    public function time($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "TIME ";
        return $this;
    }

    public function json($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "JSON ";
        return $this;
    }
    public function jsonb($column)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "JSONB ";
        return $this;
    }

    public function char($column, $length = 4)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "CHAR($length) ";
        return $this;
    }

    public function enum($column, $args)
    {
        $this->itemIndex = $column;
        $this->schema[$this->itemIndex] = "ENUM('" .  implode("', '", $args) . "') ";
        return $this;
    }

    // ==============END OF THE DATA TYPES OF THE DATABASE==============


    // ==============THESE ARE THE CONSTRAINTS OF THE DATABASE TABLE==============
    public function nullable()
    {
        $this->is_null = "NULL";
        $this->schema[$this->itemIndex] .= "$this->is_null";
        return $this;
    }
    public function unique()
    {
        $this->schema[$this->itemIndex] .= "UNIQUE ";
        return $this;
    }

    public function signed()
    {
        if (strpos($this->schema[$this->itemIndex], "UNSIGNED") !== false) {
            $this->schema[$this->itemIndex] = str_replace("UNSIGNED", "SIGNED", $this->schema[$this->itemIndex]);
        } else
            $this->schema[$this->itemIndex] .= "SIGNED ";
        return $this;
    }
    public function unsigned()
    {
        if (strpos($this->schema[$this->itemIndex], "SIGNED") !== false) {
            $this->schema[$this->itemIndex] = str_replace("SIGNED", "UNSIGNED", $this->schema[$this->itemIndex]);
        } else
            $this->schema[$this->itemIndex] .= "UNSIGNED ";
        return $this;
    }

    public function foreign()
    {
        $this->schema[$this->itemIndex] .= "FOREIGN KEY ";
        return $this;
    }
    public function references($table, $column)
    {
        $this->schema[$this->itemIndex] .= "REFERENCES $table($column) ";
        return $this;
    }
    public function onDelete($column)
    {
        $this->schema[$this->itemIndex] .= "ON DELETE $column ";
        return $this;
    }
    public function onUpdate($column)
    {
        $this->schema[$this->itemIndex] .= "ON UPDATE $column ";
        return $this;
    }
    public function onDeleteCascade()
    {
        $this->schema[$this->itemIndex] .= "ON DELETE CASCADE ";
        return $this;
    }
    public function onUpdateCascade()
    {
        $this->schema[$this->itemIndex] .= "ON UPDATE CASCADE ";
        return $this;
    }
    public function onDeleteSetNull()
    {
        $this->schema[$this->itemIndex] .= "ON DELETE SET NULL ";
        return $this;
    }
    public function onUpdateSetNull()
    {
        $this->schema[$this->itemIndex] .= "ON UPDATE SET NULL ";
        return $this;
    }
    public function onDeleteNoAction()
    {
        $this->schema[$this->itemIndex] .= "ON DELETE NO ACTION ";
        return $this;
    }
    public function onUpdateNoAction()
    {
        $this->schema[$this->itemIndex] .= "ON UPDATE NO ACTION ";
        return $this;
    }
    public function onDeleteSetDefault()
    {
        $this->schema[$this->itemIndex] .= "ON DELETE SET DEFAULT ";
        return $this;
    }
    public function onUpdateSetDefault()
    {
        $this->schema[$this->itemIndex] .= "ON UPDATE SET DEFAULT ";
        return $this;
    }
    public function onDeleteRestrict()
    {
        $this->schema[$this->itemIndex] .= "ON DELETE RESTRICT ";
        return $this;
    }
    public function onUpdateRestrict()
    {
        $this->schema[$this->itemIndex] .= "ON UPDATE RESTRICT ";
        return $this;
    }

    // ==============END OF THE CONSTRAINTS OF THE DATABASE TABLE==============



    public function timestamps()
    {
        $this->timestamps = true;
        $this->schema["created_at"] = "DATETIME DEFAULT CURRENT_TIMESTAMP ";
        $this->schema["updated_at"] = "DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ";
        return $this;
    }

    public function softDeletes()
    {
        $this->schema["deleted_at"] = "DATETIME DEFAULT NULL ";
        return $this;
    }
    public function timestamp($column)
    {
        $this->schema[$column] = "DATETIME DEFAULT CURRENT_TIMESTAMP ";
        return $this;
    }

    public function default($column)
    {
        $this->schema[$this->itemIndex] .= "DEFAULT('$column') ";
        return $this;
    }

    public function rememberToken()
    {
        $this->schema["remember_token"] = "VARCHAR(100) DEFAULT NULL ";
        return $this;
    }

    public function getSchema()
    {
        return $this->schema;
    }
}