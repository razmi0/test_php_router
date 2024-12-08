<?php

namespace App\Model\Repository;

use App\Lib\HTTP\Error;
use App\Lib\Repository;
use App\Model\Entity\Token;

/**
 * Class TokenDao
 * 
 * This class is a DAO for the token entity
 * - **create** : create a token
 * - **find** : find a token by a field
 */
class TokenRepository extends Repository
{
    /**
     * Replace the token in the database if it already exists or create a new one if not
     * 
     * @param Token $token
     * @throws \Exception 
     * @return int | false $insertedId
     */
    public function create(Token $token): int | false
    {
        try {
            $sql = "INSERT INTO T_TOKEN (jwt_value, user_id) 
                    VALUES (:jwt_value, :user_id) 
                    ON DUPLICATE KEY UPDATE jwt_value = :updated_jwt_value";


            $stmt = $this->pdo->prepare($sql);

            $token_value = $token->getTokenValue();

            $stmt->bindParam(":jwt_value", $token_value);
            $stmt->bindParam(":user_id", $token->getUserId(), \PDO::PARAM_INT);
            $stmt->bindParam(":updated_jwt_value", $token_value);

            $stmt->execute();

            $insertedId = $this->pdo->lastInsertId();

            if (!$insertedId) {
                return false;
            }

            return (int)$insertedId;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            Error::HTTP500("Erreur interne");
        } finally {
            $this->connection->close();
        }
    }

    /**
     * @param string $field
     * @param string $value
     * @throws \Exception 
     * @return Token $token
     */
    public function find(string $field, string $value): Token
    {
        try {
            $sql = "SELECT * FROM T_TOKEN WHERE $field = :value";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":value", $value);
            $stmt->execute();

            $data = $stmt->fetch();

            if (!$data) {
                Error::HTTP404("Token non trouvé", ["token" => $value]);
            }

            return Token::make($data);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            Error::HTTP500("Erreur interne");
        } finally {
            $this->connection->close();
        }
    }
}
