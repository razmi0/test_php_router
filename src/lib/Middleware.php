<?php



namespace App\Lib;

class Middleware
{
    private function __construct() {}

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
}
