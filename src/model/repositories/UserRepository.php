<?php

namespace App\Model\Repository;

use App\Lib\HTTP\Error;
use App\Lib\Repository;
use App\Model\Entity\User;

/**
 * Class UserDao
 * 
 * This class is a DAO for the user entity
 * - **create** : create a user
 * - **find** : find a user by a field
 */
class UserRepository extends Repository
{

    /**
     * Create a user
     * 
     * @param User $user
     * @throws \Exception
     * @return int|false|array $insertedId
     */
    public function create(User $user): int|false|array
    {
        try {
            $sql = "INSERT INTO T_USER (username, email, password_hash) VALUES (:username, :email, :password_hash)";
            $stmt = $this->pdo->prepare($sql);

            $username = $user->getUsername();
            $email = $user->getEmail();
            $hash = $user->getPasswordHash();

            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password_hash", $hash);

            try {
                $stmt->execute();
            } catch (\Exception $e) {
                error_log($e->getMessage());
                if ($e->getCode() === "23000") {
                    Error::HTTP400("L'email ou le nom d'utilisateur existe déjà");
                }
            }
            $insertedId = $this->pdo->lastInsertId();

            return $insertedId ? (int)$insertedId : false;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            Error::HTTP500("Erreur interne");
        } finally {
            $this->connection->close();
        }
    }

    /**
     * Find a user by a field
     * 
     * @param string $field
     * @param string $value
     * @throws \Exception 
     * @return User|false $user
     */
    public function find(string $field, string $value): User|false
    {
        try {
            $sql = "SELECT * FROM T_USER WHERE $field = :value";
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(":value", $value);
            $stmt->execute();

            $data = $stmt->fetch();

            return $data ? User::make($data) : false;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            Error::HTTP500("Erreur interne");
        } finally {
            $this->connection->close();
        }
    }
}
