<?php



namespace App\Services;

use App\Lib\HTTP\Error;
use App\Model\Entity\Token;
use App\Model\Entity\User;
use App\Model\Repository\TokenRepository;
use Firebase\JWT\JWT;

class TokenService
{

    public static function createToken(User $user, int $timestamp, int $exp, string $secret): Token
    {
        $jwt_payload = [
            "user_id" => $user->getUserId(),
            "username" => $user->getUsername(),
            "email" => $user->getEmail(),
            "iat" => $timestamp,
            "exp" => $timestamp + $exp
        ];

        $signed_jwt = JWT::encode($jwt_payload, $secret, "HS256");

        return Token::make([
            "jwt_value" => $signed_jwt,
            "user_id" => $user->getUserId()
        ]);
    }

    public static function storeTokenInDatabase(TokenRepository $token_repostory, Token $jwt): int
    {

        $token_id = $token_repostory->create($jwt);

        if (!$token_id) {
            Error::HTTP500("Le token n'a pas pu être créé en base de donnée");
        }

        return $token_id;
    }
}
