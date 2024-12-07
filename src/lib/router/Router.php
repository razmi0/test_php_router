<?php



namespace App\Lib\Routing;

use App\Lib\Controller;
use App\Lib\Injector\ContentInjector;

/**
 * @template Dep
 * @template ControllerArguments
 */
class Router
{
    private static string $view_directory_path = "/src/views";                                      // Define the views directory path
    private static string $controllers_directory_path = BASE_DIR . '/src/controllers';              // Define the controllers directory path
    private static string $controllers_namespace = 'App\\Controllers';                              // Define the controllers namespace

    /**
     * Get and run the controller that matches the request URI
     */
    public static function findController(string $uri): bool
    {
        $controllers_classes_names = self::getControllerClasses();                                                  // Get the controller classes
        foreach ($controllers_classes_names as $controller_name) {
            /**
             * @var \ReflectionClass<Controller>
             */
            $reflection_class = new \ReflectionClass($controller_name);                                         // Create a reflection class
            $controller_arguments = self::getControllerArguments($reflection_class);
            $controller_instance = new $controller_name(...$controller_arguments);                                                             // Create a new instance of the class
            $methods = $reflection_class->getMethods(\ReflectionMethod::IS_PUBLIC);                          // Get the public methods
            foreach ($methods as $method) {                                                                            // Loop through the methods
                $success = self::processControllerMethod($uri, $controller_instance, $method);         // Process the method
                if ($success) return $success;
            }
        }
        return false;
    }


    /**
     * scan the controllers directory and return the classes
     * @return class-string<Controller>[]
     */
    private static function getControllerClasses()
    {

        $classes = [];
        $iterator = new \DirectoryIterator(self::$controllers_directory_path);

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
     * @param \ReflectionClass<Controller> $controller
     * @return ControllerArguments[]
     */
    private static function getControllerArguments(\ReflectionClass $controller)
    {
        $constructor = $controller->getConstructor();
        if (!$constructor) return [];
        $params = $constructor->getParameters();
        $args = [];
        foreach ($params as $param) {
            $param_type = $param->getType();
            if ($param_type && $param_type instanceof \ReflectionNamedType) {
                $args[] = CONTAINER->get($param_type->getName());
            }
        }
        return $args;
    }

    /**
     * return false if route does not match & if file does not exist
     * return true if method is executed with DI 
     * @return bool
     */
    private static function processControllerMethod(string $uri, Controller $controller, \ReflectionMethod $method)
    {
        $route_instance = self::getAttributeInstance($method, Route::class);
        if (is_null($route_instance) || $uri !== $route_instance->path) return false; // no route or route::path does not match uri => fail

        if (!$route_instance->view) {   // no view => success
            $content = self::execute($controller, $method);
            if (is_string($content)) echo $content;
            return true;
        }

        $view_path = BASE_DIR . self::$view_directory_path . $route_instance->view;

        if (!file_exists($view_path)) return false; // view file does not exist => fail later 404
        $inject_instance = self::getAttributeInstance($method, ContentInjector::class);
        if (!$inject_instance) {
            include $view_path;
            return true; // no injector => success, include the view file
        }
        /**
         * @var string $content
         */
        $content = self::execute($controller, $method);
        echo $inject_instance->inject($view_path, $content);
        return true;
    }

    private static function execute(Controller $controller, \ReflectionMethod $method): mixed
    {
        $method_name = $method->getName();

        /** @var Dep[] */
        $deps = self::getMethodDependencies($method);
        return $controller->$method_name(...$deps);
    }


    /**
     * @return Dep[]
     */
    private static function getMethodDependencies(\ReflectionMethod $method)
    {
        /**
         * @var \ReflectionParameter[]
         */
        $params_refs = $method->getParameters();
        $dependencies = [];
        foreach ($params_refs as $param_ref) {
            $param_type = $param_ref->getType();
            if ($param_type && $param_type instanceof \ReflectionNamedType) {
                $dependencies[] = CONTAINER->get($param_type->getName());
            }
        }
        return $dependencies;
    }

    /**
     * Creates a new instance of the attribute with passed arguments
     * @template T of object
     * @param class-string<T> $attributeClass
     * @return T|null
     */
    private static function getAttributeInstance(\ReflectionMethod $method, string $attributeClass)
    {
        $attributes = $method->getAttributes($attributeClass);
        return $attributes[0] ?? null ? $attributes[0]->newInstance() : null;
    }
}
