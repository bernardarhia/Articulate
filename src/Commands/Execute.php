<?php

namespace Articulate\Command;

class Execute
{

    static function save($connection, $statement, $data = null)
    {
        $stmt = $connection->prepare($statement);
        $result = $stmt->execute($data);
        return $result;
    }
}
