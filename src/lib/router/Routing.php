<?php

namespace App\Lib\Router;

use App\Lib\HTTP\ErrorPage;
use App\Lib\Middleware;
use App\Lib\Routing\Router;
use App\Lib\Routing\StaticRoute;
use App\Lib\HTTP\Request;



/**
 * Class Routing
 * 
 * Start the application with the following:
 * - **Routing::start();**
 */
class Routing
{

    /**
     * @return array{0: string, 1: string}
     */
    private static function getUri()
    {
        $completeUri = $_SERVER['REQUEST_URI'];                                 // Get the complete URI and ensure it is a string
        if (!is_string($completeUri)) throw new \Exception("Invalid URI");
        /**
         * @var string $uri_path
         */
        $uri_path = parse_url($completeUri, PHP_URL_PATH);                            // Remove query string keeping only the path
        if (empty($uri_path)) throw new \Exception("Invalid URI");
        /**
         * @var string $extension
         */
        $extension = pathinfo($completeUri, PATHINFO_EXTENSION);                         // Get the extension
        return [
            $uri_path,                                                          // Return the URI path
            $extension,                                                         // Return the extension
        ];
    }

    public static function start(): void
    {
        [$uri_path, $extension] = self::getUri();                     // Remove query string keeping only the path
        try {
            Middleware::addDefaultHeaders();                            // Add default headers
            $success = StaticRoute::findAsset($uri_path, $extension);    // Find and serve assets
            if ($success) exit();                                   // Exit if asset is found
            $success = Router::findController($uri_path);           // Find and run controllers
            if ($success) exit();                                   // Exit if controller is found
            ErrorPage::HTTP404($uri_path);                          // 404 Not found
        } catch (\Exception $e) {
            ErrorPage::HTTP500($e);                                 // 500 Internal server error
        }
    }
}
