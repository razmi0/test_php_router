<?php

use App\Lib\HTTP\ErrorPage;
use App\Lib\Routing\Router;

require_once BASE_DIR . '/vendor/autoload.php';                                     // Require the composer autoload
require_once BASE_DIR . '/src/lib/debug/utils.php';                                 // Require the debug utilities
try {
    $uri = getUri();                                                                // Remove query string keeping only the path
    Router::addDefaultHeaders();                                                    // Add default headers
    Router::findAsset($uri);                                                        // Find and serve assets
    Router::findController($uri);                                                   // Find and run controllers
    ErrorPage::HTTP404($uri);                                                       // 404 Not found
} catch (Exception $e) {
    ErrorPage::HTTP500($e);                                                    // 500 Internal server error
}
exit();

function getUri(): string
{
    $completeUri = $_SERVER['REQUEST_URI'];                                 // Get the complete URI and ensure it is a string
    if (!is_string($completeUri)) throw new Exception("Invalid URI");
    $uri = parse_url($completeUri, PHP_URL_PATH);                            // Remove query string keeping only the path
    if (empty($uri)) throw new Exception("Invalid URI");
    return $uri;                                                            // Return the URI
}
