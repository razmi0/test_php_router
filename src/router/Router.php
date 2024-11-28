<?php



namespace App\Router;

use DirectoryIterator;
use ReflectionClass;
use Throwable;

class Router
{
    // should be in a middleware
    public static function addDefaultHeaders(): void
    {
        header("Access-Control-Allow-Origin: *");                                                   // Allow all origins
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");                    // Allow the following methods
        header(
            "Content-Security-Policy: "
                . "default-src 'self' "
                . "; object-src 'none' "
                . "; img-src 'self' "
                . "; media-src 'none' "
                . "; frame-src 'none' "
                . "; font-src 'self' "
                . "; connect-src 'self' "
                . "; script-src 'self' strict-dynamic "
                . "; style-src 'self' "
                . "; base-uri 'none' "
                . "; form-action 'self' "
                . "; frame-ancestors 'none' "
            // . "; require-trusted-types-for 'script' ;"                                          // Prevent innerHTML injections 
        );                                                                                          // Prevent XSS
        header("X-Content-Type-Options: nosniff");                                                  // Prevent MIME type sniffing
        header("Referrer-Policy: no-referrer");                                                     // Prevent referrer leakage
        header("Feature-Policy: geolocation 'none'");                                               // Prevent feature abuse
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");                    // Prevent caching
        header("Access-Control-Allow-Headers: Content-Type, Authorization");                        // Allow the following headers
    }

    /**
     * Find and serve assets
     */
    public static function findAsset(string $uri)
    {
        $uri_parts = explode('.', $uri);                                                // Split the request URI by the dot
        $uri_extension = array_pop($uri_parts);                                         // Get the last part of the request URI
        $assets_extension = ['css', 'js', 'svg', 'png', 'jpg', 'jpeg', 'gif'];          // Define the assets extensions
        $asking_asset = in_array($uri_extension, $assets_extension);                    // Check if the request URI has an extension
        if (!$asking_asset) return;
        $mimeTypes = [
            "js" => "application/javascript",
            "css" => "text/css",
            "svg" => "image/svg+xml",
            "png" => "image/png",
            "jpg" => "image/jpeg",
            "jpeg" => "image/jpeg",
            "gif" => "image/gif",
        ];                                                                              // Define the mime types
        if (isset($mimeTypes[$uri_extension]))                                          // Check if the mime type exists
            header("Content-Type: " . $mimeTypes[$uri_extension]);                      // Set the mime type
        $file_exist = file_exists(BASE_DIR . "/public" . $uri);                         // Check if the file exists
        if (!$file_exist) return;                                                       // If the file does not exist skip
        include BASE_DIR . "/public" . $uri;                                            // Include the file
        exit();                                                                         // Exit the script
    }

    /**
     * Get and run the controller that matches the request URI
     */
    public static function findController(string $uri): void
    {

        $namespace = 'App\\Controllers';                                                // Define the controllers namespace
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
                    if (!$isRoute) continue;
                    $route = $attribute->newInstance();                                         // If the attribute is not a Route attribute skip
                    $match = $uri === $route->path;                                             // Match the path with the request URI
                    if (empty($route->path) || !$match) continue;                               // If the path is empty or != than uri skip
                    $controller = new $className();                                             // Create a new instance of the class
                    if ($route->view) {                                                         // If the route has a view
                        $file_exist = file_exists(BASE_DIR . "/src/views" . $route->view);      // Check if the view file exists
                        if (!$file_exist) return;                                               // If the view file does not exist skip
                        include BASE_DIR . "/src/views" . $route->view;                         // Include the view file
                    } else {                                                                    // If the route does not have a view
                        $controller();                                                          // Run the controller (invoke the object)
                    }
                    exit();                                                             // Exit the script
                }
            }
        } catch (Throwable $th) {

            throw $th;
        }
    }
}
