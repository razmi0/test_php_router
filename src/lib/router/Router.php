<?php



namespace App\Lib\Routing;

use App\Lib\Controller;
use App\Lib\Injector\Inject;
use DirectoryIterator;
use Reflection;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use Throwable;

class Router
{
    private static string $view_directory_path = "/src/views";                                      // Define the views directory path
    private static string $controllers_directory_path = BASE_DIR . '/src/controllers';              // Define the controllers directory path
    private static string $controllers_namespace = 'App\\Controllers';                              // Define the controllers namespace
    private static string $assets_directory_path = BASE_DIR . '/public';                            // Define the assets directory path
    /** @var string[] */
    private static $assets_extensions = ['css', 'js', 'svg', 'png', 'jpg', 'jpeg', 'gif'];          // Define the assets extensions
    /** @var array<string,string> */
    private static array $assets_mime_types = [
        "js" => "application/javascript",
        "css" => "text/css",
        "svg" => "image/svg+xml",
        "png" => "image/png",
        "jpg" => "image/jpeg",
        "jpeg" => "image/jpeg",
        "gif" => "image/gif",
    ];                                                                                      // Define the assets mime types

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
                . "; style-src 'self' "
                . "; script-src 'self' 'unsafe-inline' "
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
     * Find and serve assets in /public
     */
    public static function findAsset(string $uri): void
    {
        $uri_extension = pathinfo($uri, PATHINFO_EXTENSION);                            // Get the last part of the request URI
        $asking_asset = in_array($uri_extension, self::$assets_extensions);             // Check if the request URI has an extension
        if (!$asking_asset) return;
        if (isset(self::$assets_mime_types[$uri_extension]))                            // Check if the mime type exists
            header("Content-Type: " . self::$assets_mime_types[$uri_extension]);        // Set the mime type
        $file_exist = file_exists(self::$assets_directory_path . $uri);                 // Check if the file exists
        if (!$file_exist) return;                                                       // If the file does not exist skip
        include self::$assets_directory_path . $uri;                                    // Include the file
    }

    /**
     * Get and run the controller that matches the request URI
     */
    public static function findController(string $uri): void
    {
        $controllers_classes_names = self::getControllerClasses();                                                  // Get the controller classes
        foreach ($controllers_classes_names as $controller_name) {                                                      // Iterate through each controllers file in the directory
            $reflection_class = new ReflectionClass($controller_name);                                         // Create a reflection class
            $controller_instance = new $controller_name();                                                             // Create a new instance of the class
            $methods = $reflection_class->getMethods(ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method) {
                self::processControllerMethod($uri, $controller_instance, $method);
            }
        }
    }


    /**
     * @return class-string<Controller>[]
     */
    private static function getControllerClasses()
    {

        $classes = [];
        $iterator = new DirectoryIterator(self::$controllers_directory_path);

        foreach ($iterator as $file) {
            if ($file->isDot() || $file->getExtension() !== 'php') {
                continue;
            }
            /**
             * @var class-string<Controller>
             */
            $class_name = self::$controllers_namespace . '\\' . $file->getBasename('.php');
            if (!class_exists($class_name)) continue;
            $classes[] = $class_name;
        }
        return $classes;
    }

    private static function processControllerMethod(string $uri, object $controller, ReflectionMethod $method): void
    {
        $method_name = $method->getName();
        $route_instance = self::getRouteInstance($method);
        if (is_null($route_instance) || $uri !== $route_instance->path) return;
        if (!$route_instance->view) {
            $controller->$method_name();
            return;
        }
        $view_path = BASE_DIR . self::$view_directory_path . $route_instance->view;
        if (!file_exists($view_path)) return;
        $inject_instance = self::getInjectInstance($method);
        if ($inject_instance) {
            /**
             * @var string $content
             */
            $content = $controller->$method_name();
            echo $inject_instance->inject($view_path, $content);
        } else {
            include $view_path;
        }
    }

    /**
     * @return Route|null
     */
    private static function getRouteInstance(ReflectionMethod $method)
    {
        $attributes = $method->getAttributes(Route::class);
        return $attributes[0] ?? null ? $attributes[0]->newInstance() : null;
    }

    /**
     * @return Inject|null
     */
    private static function getInjectInstance(ReflectionMethod $method)
    {
        $attributes = $method->getAttributes(Inject::class);
        return $attributes[0] ?? null ? $attributes[0]->newInstance() : null;
    }
}
