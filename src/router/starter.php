<?php

use App\Router\Router;

require_once BASE_DIR . '/vendor/autoload.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);                 // Remove query string keeping only the path
Router::addDefaultHeaders();                                                    // Add default headers
Router::findAsset($requestUri);                                                 // Find and serve assets
Router::findController($requestUri);                                            // Find and run controllers





// // Define the routes and their corresponding handlers
// $routes = [
//     '/' => [
//         'path' => 'views/home.php',
//         'headers' => []
//     ],
//     '/login' => [
//         'path' => 'views/login.php',
//         'headers' => []
//     ],
//     '/signup' => [
//         'path' => 'views/signup.php',
//         'headers' => []
//     ],
//     '/signup/submit' => [
//         'path' => 'controllers/Signup.php',
//         'headers' => []
//     ],
//     '/login/submit' => [
//         'path' => 'controllers/Login.php',
//         'headers' => []
//     ],
//     '/api/v1.0/produit/list' => [
//         'path' => 'api/v1/ListEndpoint.php',
//         'headers' => []
//     ],
//     '/api/v1.0/produit/new' => [
//         'path' => 'api/v1/CreateEndpoint.php',
//         'headers' => []
//     ],
//     '/api/v1.0/produit/update' => [
//         'path' => 'api/v1/UpdateEndpoint.php',
//         'headers' => []
//     ],
//     '/api/v1.0/produit/delete' => [
//         'path' => 'api/v1/DeleteEndpoint.php',
//         'headers' => []
//     ],
//     '/api/v1.0/produit/listone' => [
//         'path' => 'api/v1/ListOneEndpoint.php',
//         'headers' => []
//     ],
//     '/api/v1.0/produit/listmany' => [
//         'path' => 'api/v1/ListManyEndpoint.php',
//         'headers' => []
//     ],
//     // assets
//     '/styles/index.css' => [
//         'path' => 'styles/index.css',
//         'headers' => ["Content-Type: text/css"]
//     ],
//     '/images/theme-icon.svg' => [
//         'path' => 'images/theme-icon.svg',
//         'headers' => ["Content-Type: image/svg+xml"]
//     ],
//     '/js/dist/APIFetch.js' => [
//         'path' => 'js/dist/APIFetch.js',
//         'headers' => ["Content-Type: application/javascript"]
//     ],
//     '/js/dist/helpers/dom.js' => [
//         'path' => 'js/dist/helpers/dom.js',
//         'headers' => ["Content-Type: application/javascript"]
//     ],
//     '/js/dist/helpers/fetch_functions.js' => [
//         'path' => 'js/dist/helpers/fetch_functions.js',
//         'headers' => ["Content-Type: application/javascript"]
//     ],
//     '/js/dist/helpers/theme-toggle.js' => [
//         'path' => 'js/dist/helpers/theme-toggle.js',
//         'headers' => ["Content-Type: application/javascript"]
//     ],
// ];

// // Function to match the requested URI against the defined routes
// function matchRoute($requestUri, $routes)
// {
//     foreach ($routes as $pattern => $handler) {
//         // Convert the pattern to a regex pattern
//         $regexPattern = preg_replace('/\//', '\/', $pattern);
//         if (preg_match("/^$regexPattern$/", $requestUri)) {
//             return $handler;
//         }
//     }
//     return null;
// }

// // Match the requested URI to a handler
// $handler = matchRoute($requestUri, $routes);

// if ($handler) {
//     // Include the corresponding handler file
//     if (count($handler['headers']) > 0) {
//         foreach ($handler['headers'] as $header) {
//             header($header);
//         }
//     }
//     include BASE_DIR . '/' . $handler['path'];
// } else {
//     // Handle 404 Not Found
//     header("HTTP/1.0 404 Not Found");
//     echo "404 Not Found";
// }
