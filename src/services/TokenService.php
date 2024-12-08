<?php



namespace App\Services;

use App\Lib\HTTP\Error;
use App\Model\Entity\Token;
use App\Model\Entity\User;
use App\Model\Repository\TokenRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenService
{

    public static function createToken(User $user, int $exp, string $secret): Token
    {
        $timestamp = time();
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

    public static function storeTokenInDatabase(TokenRepository $token_repository, Token $jwt): int
    {

        $token_id = $token_repository->create($jwt);

        if (!$token_id) {
            Error::HTTP500("Le token n'a pas pu être créé en base de donnée");
        }

        return $token_id;
    }

    public static function deleteTokenInDatabase(TokenRepository $token_repository, string $jwt, string $secret): void
    {
        $jwt = self::splitBearer($jwt);
        $payload = JWT::decode($jwt, new Key($secret, "HS256"));
        $user_id = $payload->user_id;
        $token_repository->delete($user_id);
    }


    public static function splitBearer(string $header): string
    {
        $header_parts = explode(" ", $header);

        if (count($header_parts) !== 2) {
            Error::HTTP400("Le token n'est pas valide");
        }

        return $header_parts[1];
    }
}
