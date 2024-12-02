<?php

namespace App\Lib\Routing;

/**
 * @phpstan-type ExtensionType array<'css'|'js'|'svg'|'png'|'jpg'|'jpeg'|'gif'>
 */
class StaticRoute
{
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
}
