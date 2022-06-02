<?php

namespace App\Database;

use PDO as DBConnector;
use stdClass;
use  App\Database\Connector;

class DB
{
    private static $table = null;
    private static $query = null;
    private static $columns = null;
    private static $where;
    private static $limit;
    private static $order_by = null;
    private static $set = null;
    private static $connection = null;
    private static $group_by = null;
    private static $execute_array = [];
    private static $query_elements = [' SELECT ', ' FROM ', ' WHERE ', ' LIMIT ', ' ORDER BY ', ' GROUP BY'];
    public function __construct()
    {
        $dbHost = "localhost";
        $dbName = "altisend";
        $dbUser = "root";      //by default root is user name.  
        $dbPassword = "";     //password is blank by default  
        self::$connection = new Connector($dbName);
        return self::$connection;
    }
    public static function insert()
    {
        return new static;
    }

    /**
     * @param string ...$columns
     *  The columns names to select from the table
     * @return object
     */
    public static function select(...$columns): object
    {
        self::$query = "SELECT ";
        self::$columns = $columns;

        // if the columns array is empty, select all columns else given columns
        // check the type of data first
        if (count(self::$columns) < 1) {
            self::$columns = "*";
            self::$query .=   self::$columns;
        } else {
            self::$query .= implode(", ", self::$columns);
        }
        self::$query .=  self::$query_elements[1];
        return new static;
    }

    public static function update($table)
    {
        self::$query = "UPDATE $table SET ";
        return new static;
    }
    /** 
     * 
     * @param array $column
     * The column names to update in an associative form
     */

    public function set(array $columns)
    {
        self::$set = $columns;
        foreach (self::$set as $key => $value) {
            self::$query .= $key . " = :" . $key . ", ";
            self::$execute_array[$key] = $value;
        }
        self::$query = substr(self::$query, 0, -2);
        return new static;
    }

    public static function delete()
    {
        self::$query = "DELETE " . self::$table;
        return new static;
    }
    /**
     * @param string $table
     * @return object
     */
    public static function from($table)
    {
        self::$query .= $table;
        return new static;
    }
    /**
     * @param string|array ...$args
     * @return object
     */
    public static function where(...$args)
    {
        self::$where = $args;

        // Check and validate the where clauses
        if (!empty(self::$where)) {
            // check if params are not an array
            if (gettype(self::$where[0]) == 'string') {
                $getColumnValueType =  self::getColumnValueType(self::$where[2]);
                self::$query .= self::$query_elements[2] . self::$where[0] . " " . self::$where[1] . " :" . self::$where[0];
                self::$execute_array[self::$where[0]] = self::$where[2];
            } else {
                //    loop through the array and insert it into the query
                self::$query .= self::$query_elements[2];
                foreach (self::$where as $key => $value) {
                    self::$execute_array[$value[0]] = $value[2];
                    $getColumnValueType =  self::getColumnValueType($value[2]);
                    self::$query .=  $value[0] . " " . $value[1] . " :" . $value[0] . " AND ";
                }
                // remove the last AND in the self::$query
                self::$query = substr(self::$query, 0, -5);
            }
        }
        // print_r(self::$execute_array);

        return new static;
    }

    /**
     * @param int $limit
     * The number of rows to fetch
     */
    public function limit(int $limit)
    {
        self::$limit = $limit;
        return new static;
    }
    /**
     * @param int $column
     * The column to order by
     * @param int $ordering_type
     * The ordering type where 1 means Ascending order and 2 means Descending order
     * 
     */

    public function groupBy($column)
    {
        self::$group_by = $column;
        return new static;
    }
    public function orderBy(string $column, int $ordering_type = 1)
    {
        if ($ordering_type === 1) {
            self::$order_by = self::$query_elements[4] . $column . " ASC";
        } else {
            self::$order_by = self::$query_elements[4] . $column . " DESC";
        }
        return new static;
    }

    public function result(): string
    {

        // add limit and order by
        if (!empty(self::$group_by)) self::$query .= self::$query_elements[5] . "('" . self::$group_by . "')";
        if (!empty(self::$order_by)) self::$query .= self::$order_by;
        if (!empty(self::$limit)) self::$query .= self::$query_elements[3] . self::$limit;
        return self::$query;
    }


    // This executes delete and update queries
    public function run()
    {
        $stmt = self::$connection->prepare(self::$query);
        if (empty(self::$execute_array)) {
            $stmt->execute();
        } else {
            $stmt->execute(self::$execute_array);
        }
        return $stmt->rowCount();
        print_r(self::$execute_array);
    }
    public function first()
    {
        $stmt = self::$connection->prepare(self::$query);
        if (empty(self::$execute_array)) {
            $stmt->execute();
        } else {
            $stmt->execute(self::$execute_array);
        }
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(DBConnector::FETCH_OBJ);
        } else {
            return new stdClass;
        }
    }
    /**
     * 
     * @param string|int $value
     * @return string|int
     */
    private static  function getColumnValueType(string | int $value): string | int
    {
        if (gettype($value) === "string" && strpos($value, "(") === false & gettype($value) !== "bool") {
            return "'" . $value . "'";
        } else {
            return $value;
        }
    }
    public static function db()
    {
        return self::$connection;
    }
}