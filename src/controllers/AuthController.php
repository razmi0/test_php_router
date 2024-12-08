<?php

namespace App\Controllers;

use App\Lib\Controller;
use App\Lib\HTTP\Error;
use App\Lib\Injector\ContentInjector;
use App\Lib\Injector\JS;
use App\Lib\Routing\Route;
use App\Model\Entity\User;
use App\Model\Repository\TokenRepository;
use App\Model\Repository\UserRepository;
use App\Services\TokenService;

class AuthController extends Controller
{
    public const EXPIRATION_TOKEN = 60 * 60 * 24 * 7;  // 1 week
    public const AUTH_COOKIE_NAME = "auth_token";
    public const AUTH_COOKIE = [
        "path" => "/",
        "domain" => "localhost",
        "secure" => true,
        "httponly" => true,
        "samesite" => "Strict"
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
        $client_data["password_hash"] = password_hash($client_data["password"], PASSWORD_DEFAULT);

        $user = User::make($client_data);
        $user_id = $user_repository->create($user);

        if (!$user_id)
            Error::HTTP500("Erreur lors de la création de l'utilisateur");

        $payload =  [
            "user" => [
                "user_id" => $user_id,
                "username" => $user->getUsername(),
                "email" => $user->getEmail()
            ],
        ];

        $this->response
            ->setCode(303)
            ->setMessage("Utilisateur créé avec succès")
            ->setLocation("/login")
            ->setPayload($payload)
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
        $user = $user_repository->find("email", $client_data["email"]);                            // Fetch user with dao
        $password_hash = $user->getPasswordHash();

        if (!$user || !password_verify($client_data["password"], $password_hash))           // if user not found or password invalid
            Error::HTTP401("Identifiants invalides");

        $timestamp = time();                                                                // Get current timestamp
        $signed_token = TokenService::createToken(
            $user,
            $timestamp,
            self::EXPIRATION_TOKEN,
            $_ENV["TOKEN_GENERATION_KEY"]
        );
        $token_id = TokenService::storeTokenInDatabase($token_repository, $signed_token);                        // Store token in database

        $signed_token->setTokenId($token_id);                                               // Set token id in token object

        $auth_cookie =                                                                      // Create auth cookie
            [
                self::AUTH_COOKIE_NAME,
                "Bearer " . $signed_token->getTokenValue(),
                [
                    ...self::AUTH_COOKIE,
                    "expires" => $timestamp + self::EXPIRATION_TOKEN
                ]
            ];

        $payload = [                                                                        // Create payload for response
            "user" => [
                "user_id" => $user->getUserId(),
                "username" => $user->getUsername(),
                "email" => $user->getEmail(),
                "token_id" => $signed_token->getTokenId()
            ]
        ];

        session_start();                                                                    // Start session
        $_SESSION["id"] = $user->getUserId();                                               // Set user in session

        $this->response                                                                      // Set auth cookie in response
            ->setCode(303)
            ->setMessage("Connexion réussie")
            ->setLocation("/")
            ->addCookies($auth_cookie)
            ->setPayload($payload)                                                           // Set payload in response
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
}
