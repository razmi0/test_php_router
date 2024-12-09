<?php

namespace App\Controllers;

use App\Lib\Controller;
use App\Lib\HTTP\Error;
use App\Lib\Routing\Route;
use App\Model\Entity\User;
use App\Model\Repository\TokenRepository;
use App\Model\Repository\UserRepository;
use App\Services\TokenService;
use Valitron\Validator;

/**
 * Class AuthController
 * 
 * - routes : 
 *      - /signup
 *      - /signup/submit
 *      - /login
 *      - /login/submit
 *      - /logout
 * 
 */
class AuthController extends Controller
{
    public const EXPIRATION_TOKEN = 60 * 30;  // 30 minutes
    public const AUTH_COOKIE_NAME = "auth_token";
    public const AUTH_COOKIE = [
        "path" => "/",
        "domain" => "localhost",
        "secure" => true,
        "httponly" => true,
        "samesite" => "Strict"
    ];
    const RULES = [
        "username" => ["required", ["lengthBetween", 3, 50], ["regex", "/^[a-zA-Z0-9]+$/"]],
        "email" => ["required", "email"],
        "password" => ["required", ["lengthBetween", 8, 50], ["regex", "/^[a-zA-Z0-9]+$/"]]
    ];

    #[Route(path: "/signup", view: "/signup.php")]
    public function signupView(): void {}

    #[Route(path: "/signup/submit")]
    public function signup(UserRepository $user_repository)
    {
        $this->middleware
            ->checkAllowedMethods(["POST"])
            ->sanitizeData(["sanitize" => ["html", "integer", "float"]]);

        $client_data = $this->request->getDecodedData();

        [$isValid, $errors] = $this->runValidation(self::RULES, $client_data);                         // Validate client data
        if (!$isValid) {
            Error::HTTP400("Données invalides", $errors);
        }

        $client_data["password_hash"] = password_hash($client_data["password"], PASSWORD_DEFAULT);

        $user = User::make($client_data);
        $user_repository->create($user);

        $this->response
            ->setCode(303)
            ->setLocation("/login")
            ->send();
    }

    #[Route(path: "/login", view: "/login.php")]
    public function loginView(): void {}


    #[Route(path: "/login/submit")]
    public function login(UserRepository $user_repository, TokenRepository $token_repository): void
    {
        $this->middleware
            ->checkAllowedMethods(["POST"])
            ->sanitizeData([
                "sanitize" => [
                    "html",
                    "integer",
                    "float"
                ]
            ]);

        $client_data = $this->request->getDecodedData();                                    // Get client data
        [$isValid, $errors] = $this->runValidation(self::RULES, $client_data);                         // Validate client data
        if (!$isValid) {
            Error::HTTP400("Données invalides", $errors);
        }
        $user = $user_repository->find("email", $client_data["email"]);                            // Fetch user with dao

        if (!$user) {
            Error::HTTP401("Identifiants invalides");
        }

        $password_hash = $user->getPasswordHash();

        if (!$user || !password_verify($client_data["password"], $password_hash))           // if user not found or password invalid
            Error::HTTP401("Identifiants invalides");

        $signed_token = TokenService::createToken(
            $user,
            self::EXPIRATION_TOKEN,
            $_ENV["TOKEN_GENERATION_KEY"]
        );
        $token_id = TokenService::storeTokenInDatabase($token_repository, $signed_token);                        // Store token in database

        $signed_token->setTokenId($token_id);                                               // Set token id in token object

        $timestamp = time();                                                                // Get current timestamp
        $auth_cookie =                                                                      // Create auth cookie
            [
                self::AUTH_COOKIE_NAME,
                "Bearer " . $signed_token->getTokenValue(),
                [
                    ...self::AUTH_COOKIE,
                    "expires" => $timestamp + self::EXPIRATION_TOKEN
                ]
            ];

        session_start();                                                                    // Start session
        $_SESSION["id"] = $user->getUserId();                                               // Set user in session

        $this->response                                                                      // Set auth cookie in response
            ->setCode(303)
            ->setLocation("/")
            ->addCookies($auth_cookie)
            ->send();                                                                        // Send response
    }

    #[Route(path: "/logout")]
    public function logout(TokenRepository $tokenRepository): void
    {
        $this->middleware
            ->checkAllowedMethods(["GET"]);

        $auth_cookie = $this->request->getCookie(self::AUTH_COOKIE_NAME);                   // Get auth cookie
        TokenService::deleteTokenInDatabase($tokenRepository, $auth_cookie, $_ENV["TOKEN_GENERATION_KEY"]); // Delete token in database

        session_start();
        session_unset();
        session_destroy();
        $_SESSION = [];

        $this->response
            ->setCode(303)
            ->deleteCookie(self::AUTH_COOKIE_NAME)
            ->setMessage("Déconnexion réussie")
            ->setLocation("/login")
            ->send();
    }

    private static function runValidation(array $rules, mixed $data): array
    {
        $validator =  new Validator();

        $validator->mapFieldsRules($rules);

        $validator = $validator->withData($data);

        return [$validator->validate(), $validator->errors()];
    }
}
