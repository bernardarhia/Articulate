<?php

namespace Articulate\Database;

use Articulate\Commands\KEYWORDS;
use Articulate\Helpers\Error;
use Articulate\Articulate;

class Model extends Articulate
{
    private static $executedArray = [];
    private static $result = [];
    private static $executed = null;
    protected static $className = null;
    static private $fetch_all = false;
    static private $fetch_one = false;
    static protected $query = null;
    static function join($tableName, $on)
    {
        self::$query .= " INNER JOIN `$tableName` ON ";
        foreach ($on as $key => $value) {
            self::$query .= "$key = $value";
        }
        return new static;
    }


    static function all($options = null, $callback = null)
    {
        // print_r(self::$connection);
        // self::reset();
        self::$fetch_all = true;
        try {


            // get parent class of the called class
            $calledClass = get_called_class();
            self::$className = strtolower($calledClass);
            // all data
            self::$query = KEYWORDS::SELECT . " ";
            // check if it's an array
            if (is_null($options) || empty(($options)) || count($options) == 0) {
                self::$query .= "*";
            }

            if (is_array($options)) {
                foreach ($options as $key => $value) {
                    self::$query .= "$value, ";
                }
                self::$query = substr(self::$query, 0, strlen(self::$query) - 2);
            }
            // If data passed in is a string

            self::$query .= " from `" . strtolower(self::$className) . "`";
        }
        // Catch error
        catch (\Throwable $th) {
            self::$error = Error::createError($th->getMessage(), Error::SELECT_ERROR);
        }
        if (is_callable($callback)) {
            $callback(self::$result, (object)self::$error);
        }
        // self::$className = "";
        unset($calledClass);
        return new static;
    }
    static function one($options = null, $callback = null)
    {
        // self::reset();
        self::$fetch_one = true;
        try {
            // get parent class of the called class
            $calledClass = get_called_class();
            self::$className = strtolower($calledClass);
            self::$query = KEYWORDS::SELECT . " ";
            // check if it's an array
            if (is_null($options) || empty(($options)) || count($options) == 0) {
                self::$query .= "*";
            }

            if (is_array($options)) {
                foreach ($options as $key => $value) {
                    self::$query .= "$value, ";
                }
                self::$query = substr(self::$query, 0, strlen(self::$query) - 2);
            }
            // If data passed in is a string

            self::$query .= " from `" . strtolower(self::$className) . "`";
        }
        // Catch error
        catch (\Throwable $th) {
            self::$error = Error::createError($th->getMessage(), Error::SELECT_ERROR);
        }
        if (is_callable($callback)) {
            $callback(self::$result, (object)self::$error);
        }
        return new static;
    }

    static function update($rules)
    {

        self::reset();
        // get parent class of the called class
        $calledClass = get_called_class();
        self::$className = strtolower($calledClass);
        if (!is_array($rules) || array_values($rules) == $rules) throw new \Exception("Update error");

        self::$query = KEYWORDS::UPDATE . " " . strtolower(self::$className) . " " . KEYWORDS::SET . " ";
        // self::$query .= implode(",", ($rules));
        foreach ($rules as $key => $value) {
            self::$query .= "`$key` = :$key, ";
            self::$executedArray[":$key"] = $value;
        }
        self::$query = substr(self::$query, 0, strlen(self::$query) - 2);
        return new static;
    }

    static function delete($options = null)
    {
        self::reset();
        try {
            // get parent class of the called class
            $calledClass = get_called_class();
            self::$className = strtolower($calledClass);
            self::$query = KEYWORDS::DELETE . " FROM `" . strtolower(self::$className) . "`";
        }
        // Catch error
        catch (\Throwable $th) {
            self::$error = Error::createError($th->getMessage(), Error::DELETE_ERROR);
        }
        return new static;
    }

    function or($data = [])
    {
        // Remove or and from the end of the query
        self::$query = trim(self::removeAndOr(self::$query));
        self::$query .= " OR ";
        return new static;
    }

    static function where(...$args)
    {
        if (empty($args) || count($args) == 0) throw new \Exception("No data passed in");

        // Remove or and from the end of the query
        $split = explode(" ", trim(self::$query));
        $lastWord = trim($split[count($split) - 1]);

        // Check if string has the key word AND or OR then remove them
        if (!in_array(strtolower($lastWord), ["or", "and"])) {
            self::$query .= " " . KEYWORDS::WHERE . " ";
        }

        // check if the args passed was one and if it was an array
        if (count($args) == 1 && is_array($args[0])) {
            $args = $args[0];
            /**
             * Loop through the array and create key value pair for each item
             * but if the value is an array then loop through it and map it to the key of the array
             * 
             */
            foreach ($args as $key => $value) {
                // Check if the value passed was a string
                if (is_string($value)) {
                    self::$query .= ("`$key` = :$key AND ");
                    self::$executedArray[":$key"] = $value;
                }
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        // IF the items passed are normal array items
                        if (is_numeric($k) && is_object($v)) {
                            if (isset($v->operator)) {
                                self::$query .= ("$key $v->operator :$key AND ");
                                self::$executedArray[":$key"] = $v->value;
                            } else if (isset($v->sql)) {
                                self::$query .= ("$key $v->sql AND ");
                                self::$executedArray[":__btw1"] = $v->params->btw1;
                                self::$executedArray[":__btw2"] = $v->params->btw2;
                            }
                        } else if (is_numeric($k) && is_string($v)) {
                            self::$query .= ("$key = '$v' AND ");
                        }
                    }
                }
                if (is_object($value)) {
                    if (isset($value->sql))
                        self::$query .= ("$key $value->sql AND ");
                    self::$executedArray[":__btw1"] = $v->params->btw1;
                    self::$executedArray[":__btw2"] = $v->params->btw2;
                    if (isset($value->operator)) {
                        self::$query .= ("$key $value->operator :$key AND ");
                        self::$executedArray[":$key"] = $value->value;
                    }
                }
            }
        }

        /**
         * 
      Arguments are two example (id, 1) or  (id, [1,2,3])
         */
        else if (count($args) == 2) {
            if (!is_array($args[1])) {
                self::$query .= "(`$args[0]` = :$args[0]) AND ";

                if (is_object($args[1])) {
                    if (isset($args[1]->operator)) {
                        self::$executedArray[":$args[0]"] = $args[1]->value;
                    }
                } else
                    self::$executedArray[":$args[0]"] = $args[1];
            }
        }
        /**
         * 
      Arguments are two example (id,>, 1)
         */
        else if (count($args) == 3) {
            self::$query .= "(" . $args[0] . " " . $args[1] . " :" . $args[0] . ") AND";
            self::$executedArray[":$args[0]"] = $args[2];
        }
        return new static;
    }
    function limit($number)
    {
        self::$query .= " " . KEYWORDS::LIMIT . "  $number";
        return new static;
    }
    function groupBy($field)
    {
        self::$query = trim(self::removeAndOr(self::$query));
        self::$query .= " " . KEYWORDS::GROUP_BY . " $field";
        return new static;
    }
    function orderBy($field, $sortType = "ASC")
    {
        self::$query = trim(self::removeAndOr(self::$query));
        self::$query .= " " . KEYWORDS::ORDER_BY . " $field $sortType";
        return new static;
    }

    function get($callback = null)
    {
        // Remove or and from the end of the query
        static::$query  = static::getQuery() . ";";

        // print_r(static::$query);
        // die;
        try {
            $stmt = static::$connection->prepare(static::$query);
            $stmt->execute(static::$executedArray);

            if (static::$fetch_all) static::$result = $stmt->fetchAll(Connector::FETCH_OBJ);
            else if (static::$fetch_one) static::$result = $stmt->fetch(Connector::FETCH_OBJ);
        } catch (\Throwable $th) {
            self::$error = Error::createError($th->getMessage(), Error::INSERT_ERROR);
        } finally {
            static::reset();
        }
        if (is_callable($callback)) {
            $callback((object)self::$error);
        }
        return (object) self::$result;
    }
    function run($callback = null)
    {
        // Remove or and from the end of the query
        static::$query  = static::removeAndOr(static::$query);

        try {
            $stmt = static::$connection->prepare(static::$query);
            $executed = $stmt->execute(static::$executedArray);

            if ($executed) static::$result = ["success" =>  $stmt->rowCount() ?? true, "affectedRows" => $stmt->rowCount()];
            else static::$result = ["success" => false, "error" => $stmt->errorInfo()];
        } catch (\Throwable $th) {
            self::$error = Error::createError($th->getMessage(), Error::INSERT_ERROR);
        } finally {
            static::reset();
        }
        if (is_callable($callback)) {
            $callback((object)self::$error);
        }
        if (!empty(self::$result)) return (object) self::$result;

        return false;
    }

    protected static function removeAndOr($query)
    {
        $split = explode(" ", trim($query));
        $lastWord = ($split[count($split) - 1]);
        $lastWordCount  = strlen($lastWord);

        if (in_array(strtolower($lastWord), ["and", "or"])) {
            $query = substr($query, 0, -$lastWordCount - 1);
        }
        return $query;
    }

    static function sum($column, $alias = null)
    {
        self::$query .= KEYWORDS::SUM . " (`$column`) " . ($alias ? "AS $alias" : "");
        return new static;
    }
    static function count($column, $alias = null)
    {
        self::$query .= KEYWORDS::COUNT . " (`$column`) " . ($alias ? "AS $alias" : "");
        return new static;
    }
    static function avg($column, $alias = null)
    {
        self::$query .= KEYWORDS::AVG . " (`$column`) " . ($alias ? "AS $alias" : "");
        return new static;
    }

    function getQuery()
    {
        return  strtolower(static::removeAndOr(static::$query));
    }

    static function create($data)
    {
        self::$query = "INSERT INTO " . strtolower(self::$className) . " (";
        self::$executedArray = [];
        foreach ($data as $key => $value) {
            self::$query .= "`$key`,";
            self::$executedArray[":$key"] = $value;
        }
        self::$query = trim(self::$query, ",");
        self::$query .= ") VALUES (";
        foreach (self::$executedArray as $key => $value) {
            self::$query .= "$key,";
        }
        self::$query = trim(self::$query, ",");
        self::$query .= ")";

        return new static;
    }
    static function reset()
    {
        // self::$data = null;
        self::$executedArray = null;
        self::$executed = null;
        self::$query = null;
        // self::$connection = null;
        self::$fetch_all = null;
        self::$fetch_one = null;
        // self::$className = null;
    }
}
