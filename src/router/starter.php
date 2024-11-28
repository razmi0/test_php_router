<?php

use App\Router\{Route};

require_once BASE_DIR . '/vendor/autoload.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");


$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);                 // Remove query string keeping only the path
$assets_extensions = ['css', 'js', 'svg', 'png', 'jpg', 'jpeg', 'gif'];         // Define the assets extensions

print_r("[Router] : request URI is $requestUri" . PHP_EOL);                     // Print the request URI
$uri_parts = explode('.', $requestUri);                                         // Split the request URI by the dot
$uri_extension = array_pop($uri_parts);                                        // Get the last part of the request URI
$ask_asset = in_array($uri_extension, $assets_extensions);                        // Check if the request URI has an extension

if ($ask_asset) {
    print_r("[Router] : looking for : $uri_extension" . PHP_EOL);                     // Print the ask asset

    $public_directory = BASE_DIR . '/public';                                       // Define the public directory
    /**
     * @see https://stackoverflow.com/questions/12077177/how-does-recursiveiteratoriterator-work-in-php
     */
    $rec_iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($public_directory)); // Create a recursive iterator
}

$controller = findController($requestUri);

/**
 * Get and run the controller that matches the request URI
 */
function findController(string $requestUri): void
{
    // $views = 'App\\Views';
    // $views_directory = BASE_DIR . '/src/Views';
    // $views_iterator = new DirectoryIterator($views_directory);

    $controller = null;                                                            // Define the controller
    $namespace = 'App\\Controllers';                                              // Define the controllers namespace
    $controllers_directory = BASE_DIR . '/src/Controllers';                         // Define the controllers directory
    $controllers_iterator = new DirectoryIterator($controllers_directory);          // Create a directory iterator (not recursive)
    try {
        foreach ($controllers_iterator as $file) {                              // Iterate through each controllers file in the directory
            if ($file->isDot() || $file->getExtension() !== 'php')              // Skip dot files and non-php files
                continue;
            $className = $namespace . '\\' . $file->getBasename('.php');        // Get the class name with namespace from the file name
            $reflectionClass = new ReflectionClass($className);                 // Create a reflection class
            $attributes = $reflectionClass->getAttributes(Route::class);        // Get the attributes
            if (empty($attributes)) continue;                                   // If the class does not have attributes skip

            foreach ($attributes as $attribute) {                               // Iterate through the attributes
                $isRoute = $attribute->getName() === 'App\Router\Route';
                if (!$isRoute) continue;                                            // If the attribute is not a Route attribute skip
                $path = $attribute->newInstance()->path;                            // Get the path from a Route instance
                $match = $requestUri === $path;                                           // Match the path with the request URI
                if (empty($path) || !$match) continue;                              // If the path is empty or != than uri skip
                print_r(
                    "[Router] : found match for $requestUri"
                        . PHP_EOL
                        . "[Router] : returning $className"
                        . PHP_EOL
                );                                                                  // Print the match
                $controller = new $className();                                     // Create a new instance of the class
                $controller->handle();                                            // Run the handle method
            }
        }
    } catch (Throwable $th) {

        throw $th;
    }
}

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
