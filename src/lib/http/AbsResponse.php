<?php

namespace App\Lib\HTTP;



/**
 * @phpstan-type PayloadType array{message: string, data: mixed, error: string} | array{}
 * @phpstan-type ConfigKeys key-of<ConfigType>
 * @phpstan-type ConfigType array{code: int, message: string, data: PayloadType, error: string, content_type: string, origin: string, methods: string[], age: int, location?: string}
 * @phpstan-type CookieType array{string, string, array{expires: int, path: string, domain: string, secure: bool, httponly: bool}} | array{}
 */
class AbsResponse
{
    protected ?int $code = null;
    /**
     * @var array<string, int|string>
     */
    protected array $header = [];
    /**
     * @var PayloadType $payload
     */
    protected array $payload = [];
    /**
     * @var CookieType[] $cookies
     */
    protected array $cookies = [];
    /**
     * @var ConfigType $config
     */
    protected array $config = [
        "code" => 200,
        "message" => "",
        "data" => [],
        "error" => "",
        "content_type" => "application/json",
        "origin" => "*",
        "methods" => ["GET", "POST", "PUT", "DELETE"],
        "age" => 3600,
    ];

    public function __clone()
    {
        throw new \Exception("Cannot clone a singleton");
    }

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton");
    }

    public function __sleep()
    {
        throw new \Exception("Cannot serialize a singleton");
    }
    /**
     * @param string[] $methods
     */
    protected static function methodsToString(array $methods): string
    {
        return implode(", ", $methods);
    }
}
