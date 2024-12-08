<?php

namespace App\Model\Entity;

/**
 * Class Token
 * @property string $token_id
 * @property string $jwt_value
 * @property string $user_id
 * @property string $created_at
 * @property string $updated_at
 * 
 * - **getTokenId**
 * - **getTokenValue**
 * - **getUserId**
 * - **getCreatedAt**
 * - **getUpdatedAt**
 * - **setTokenId**
 * - **setTokenValue**
 * - **setUserId**
 * - **setCreatedAt**
 * - **setUpdatedAt**
 * - **toArray** : convert the object to an array
 */
class Token
{
    public function __construct(
        private ?string $token_id = null,
        private ?string $jwt_value = null,
        private ?string $user_id = null,
        private ?string $created_at = null,
        private ?string $updated_at = null
    ) {}


    public static function make(array $data)
    {
        return new Token(
            $data["token_id"] ?? null,
            $data["jwt_value"] ?? null,
            $data["user_id"] ?? null,
            $data["created_at"] ?? null,
            $data["updated_at"] ?? null
        );
    }

    /**
     * Getters
     */
    public function getTokenId(): ?string
    {
        return $this->token_id;
    }

    public function getTokenValue(): ?string
    {
        return $this->jwt_value;
    }

    public function getUserId(): ?string
    {
        return $this->user_id;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }

    /**
     * Setters
     */

    public function setTokenId(string $token_id): self
    {
        $this->token_id = $token_id;
        return $this;
    }


    public function setTokenValue(string $jwt_value): self
    {
        $this->jwt_value = $jwt_value;
        return $this;
    }

    public function setUserId(string $user_id): self
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function setCreatedAt(string $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function setUpdatedAt(string $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    public static function toArray(Token $token): array
    {
        return [
            'token_id' => $token->getTokenId(),
            'jwt_value' => $token->getTokenValue(),
            'user_id' => $token->getUserId(),
            'created_at' => $token->getCreatedAt(),
            'updated_at' => $token->getUpdatedAt()
        ];
    }
}
