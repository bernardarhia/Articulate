<?php

namespace Articulate;


use PDO;

class Articulate
{
    protected static $connection = null;
    protected static $error = [];
    static function connection($connectionString = null,  $callback = null)
    {
        $connectionString = trim($connectionString);


        if (is_null($connectionString) || empty($connectionString)) {
            throw new \Exception("No connection string found", 1);
        }

        $splittedStrings = explode("/", trim($connectionString));
        $filteredArray = array_filter($splittedStrings, function ($arr) {
            return $arr;
        });

        if (count($filteredArray) < 3) throw new \Exception("Connection string needs at least three values", 1);

        $dialect = $filteredArray[0] ?? null;
        $host = $filteredArray[1] ?? null;
        $user = $filteredArray[2] ?? null;
        $database = $filteredArray[3] ?? null;
        $password = $filteredArray[4] ?? "";
        // Form pdo connection string
        $pdoConnectionString = "";
        if ($dialect) $pdoConnectionString .= "mysql:";
        if ($host) $pdoConnectionString .= "host=" . $host . ";";
        if ($user) $pdoConnectionString .= "user=" . $user . ";";
        if ($database) $pdoConnectionString .= "dbname=" . $database . ";";
        if ($password) $pdoConnectionString .= "password=" . $password . ";";

        try {
            //code...
            self::$connection = new PDO($pdoConnectionString);
        } catch (\Throwable $th) {
            $callback($th->getMessage());
        }
    }
    static function  getConnection()
    {
        return self::$connection;
    }
}