<?php

namespace App\Lib;

use App\Lib\HTTP\Error;
use App\Lib\HTTP\ErrorPage;
use App\Lib\HTTP\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key as JWTKey;

class Middleware
{
    public function __construct(private Request $request) {}

    public static function addDefaultHeaders(): void
    {
        header("Access-Control-Allow-Origin: *");                                                   // Allow all origins
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");                    // Allow the following methods
        header("X-Content-Type-Options: nosniff");                                                  // Prevent MIME type sniffing
        header("Referrer-Policy: no-referrer");                                                     // Prevent referrer leakage
        header("Feature-Policy: geolocation 'none'");                                               // Prevent feature abuse
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");                    // Prevent caching
        header("Access-Control-Allow-Headers: Content-Type, Authorization");                        // Allow the following headers
    }

    public function checkAuthorization(): self
    {
        $auth_header_value = $this->request->getHeader('Authorization');                        // Get Authorization header
        $auth_cookie_value = $this->request->getCookie('auth_token');                           // Get auth_token cookie

        if ($auth_cookie_value) $token_value = $auth_cookie_value;
        else if ($auth_header_value) $token_value = $auth_header_value;
        else $token_value = null;                                                               // Get token value

        if (!$token_value)                                                                      // Check if both are missing
        {
            Error::HTTP400("Aucun header Authorization ou cookie auth_token n'a été trouvé");
            return $this;                                                                       // Return 400 error
        }   // Return 400 error


        /**
         * @var string $jwt
         */
        $jwt = str_replace("Bearer ", "", $token_value);                                        // Remove "Bearer " prefix
        /**
         * @var string $key_material
         */
        $key_material = $_ENV["TOKEN_GENERATION_KEY"];                                                   // Get JWT key
        $jwt_key = new JWTKey($key_material, "HS256");                          // Create JWT key

        try {
            $decoded_token = (array)JWT::decode($jwt, $jwt_key);                                // Decode JWT token
            foreach ($decoded_token as $key => $value) {                                        // Iterate over token data
                $this->request->addAttribute($key, $value);                                     // Add data to request
            }
        } catch (\Exception $e) {                                                                // Catch decoding exceptions
            $exception_name = (new \ReflectionClass($e))->getShortName();                       // Get exception name
            Error::HTTP401($e->getMessage(), ["exception" => $exception_name]);                 // Return 401 error
        }

        return $this;                                                                           // Return self
    }


    /**
     * @param array<string> $allowedMethods
     */
    public function checkAllowedMethods(array $allowedMethods): self
    {
        if (!in_array($this->request->getRequestMethod(), $allowedMethods)) {
            $error_message = "Seules les méthodes suivantes sont autorisées : " . implode(", ", $allowedMethods);
            Error::HTTP405($error_message, []);
        }
        return $this;
    }



    public function checkValidJson(): self
    {
        if (!$this->request->getIsValidJson()) {
            $error_message = $this->request->getJsonErrorMsg();
            Error::HTTP400("Données invalides " . $error_message, []);
        }
        return $this;
    }


    /**
     * @param array<string, array<string>> $config
     */
    public function sanitizeData($config): self
    {
        $client_data = $this->request->getDecodedData();
        $rules = $config['sanitize'];

        $sanitize_recursively = function ($data) use (&$rules, &$sanitize_recursively) {
            return match (gettype($data)) {
                'array' => array_map($sanitize_recursively, $data),
                'string' => in_array('html', $rules) ? trim(strip_tags($data)) : $data,
                'integer' => in_array('integer', $rules) ? filter_var($data, FILTER_SANITIZE_NUMBER_INT) : $data,
                'double' => in_array('float', $rules) ? filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : $data,
                default => $data,
            };
        };

        $this->request->setDecodedBody($sanitize_recursively($client_data));
        return $this;
    }
}
