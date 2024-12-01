<?php

namespace App\Lib;

use Exception;
use PDO;
use PDOException;


/**
 * Class Connection
 * 
 * Has some presets for dev PDO connection 
 * - **getPDO** : return the PDO object
 * - **closeConnection** : close the connection
 * - **setDbName** : set the database name
 * - **__construct** : create a new PDO connection
 */
class Connection
{
    public function __construct(
        private ?PDO $pdo = null,
        private string $host = "localhost",
        private int $port = 3306,
        private string $username = "root",
        private string $password = "",
        private string $db_name = "db_labrest",
        private string $charset = "utf8mb4",
        private string $collation = "utf8mb4_unicode_ci",
        private string $tech = "mysql",
    ) {
        try {
            $this->pdo = self::setPDOAttributes(
                new PDO(
                    dsn: "$this->tech:host=$this->host:$this->port;dbname=$this->db_name;charset=$this->charset;collation=$this->collation",
                    username: $this->username,
                    password: $this->password,
                )
            );
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    public function setDbName(string $db_name): self
    {
        $this->db_name = $db_name;
        return $this;
    }

    public function getPDO(): PDO
    {
        if (is_null($this->pdo)) {
            throw new Exception("Connection is not established");
        }
        return $this->pdo;
    }

    public function close(): self
    {
        $this->pdo = null;
        return $this;
    }

    private static function setPDOAttributes(PDO $pdo): PDO
    {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    }
}
