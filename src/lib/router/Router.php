<?php



namespace App\Lib\Routing;

use App\Lib\Controller;
use App\Lib\Injector\Inject;
use DirectoryIterator;
use ReflectionClass;
use ReflectionMethod;

/**
 * @phpstan-type ExtensionType array<'css'|'js'|'svg'|'png'|'jpg'|'jpeg'|'gif'>
 */
class Router
{
    private static string $view_directory_path = "/src/views";                                      // Define the views directory path
    private static string $controllers_directory_path = BASE_DIR . '/src/controllers';              // Define the controllers directory path
    private static string $controllers_namespace = 'App\\Controllers';                              // Define the controllers namespace
    private static string $public_directory_path = BASE_DIR . '/public';                            // Define the assets directory path
    /**
     * @var ExtensionType 
     */
    private static $assets_extensions = ['css', 'js', 'svg', 'png', 'jpg', 'jpeg', 'gif'];          // Define the assets extensions
    /**
     * @var array<key-of<ExtensionType>,string>
     */
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
    public static function findAsset(string $uri, string $extension): bool
    {
        header("Debugger-Find-Asset: uri= $uri,extension= $extension");
        $asking_asset = in_array($extension, self::$assets_extensions);             // Check if the request URI has an extension
        if (!$asking_asset) return false;
        if (isset(self::$assets_mime_types[$extension]))                            // Check if the mime type exists
            header("Content-Type: " . self::$assets_mime_types[$extension]);        // Set the mime type
        $file_exist = file_exists(self::$public_directory_path . $uri);                 // Check if the file exists
        if (!$file_exist) return false;                                                       // If the file does not exist skip
        include self::$public_directory_path . $uri;                                    // Include the file
        return true;
    }

    /**
     * Get and run the controller that matches the request URI
     */
    public static function findController(string $uri): bool
    {
        $controllers_classes_names = self::getControllerClasses();                                                  // Get the controller classes
        foreach ($controllers_classes_names as $controller_name) {
            /**
             * @var ReflectionClass<Controller>
             */
            $reflection_class = new ReflectionClass($controller_name);                                         // Create a reflection class
            $controller_instance = new $controller_name();                                                             // Create a new instance of the class
            $methods = $reflection_class->getMethods(ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method) {
                $success = self::processControllerMethod($uri, $controller_instance, $method);
                if ($success) return $success;
            }
        }
        return false;
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

    /**
     * @return bool
     */
    private static function processControllerMethod(string $uri, Controller $controller, ReflectionMethod $method)
    {
        $method_name = $method->getName();
        $route_instance = self::getRouteInstance($method);
        if (is_null($route_instance) || $uri !== $route_instance->path) return false;
        if (!$route_instance->view) {
            $controller->$method_name();
            return true;
        }
        $view_path = BASE_DIR . self::$view_directory_path . $route_instance->view;
        if (!file_exists($view_path)) return false;
        $inject_instance = self::getInjectInstance($method);
        if (!$inject_instance) {
            include $view_path;
            return true;
        }
        /**
         * @var string $content
         */
        $content = $controller->$method_name();
        echo $inject_instance->inject($view_path, $content);
        return true;
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
