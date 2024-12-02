<?php

namespace App\Lib;

use PDO;

abstract class Repository
{
    protected PDO $pdo;
    public function __construct(protected Connection $connection)
    {
        $this->pdo = $this->connection->getPDO();
    }
}
