<?php

namespace App\Lib\HTTP;



/**
 * @phpstan-type PayloadType array{message: string, data: mixed, error: string} | array{}
 */
interface IResponse
{
    public function setCode(int $code): self;
    public function setMessage(string $message): self;
    /**
     * @param PayloadType $data
     */
    public function setPayload(array $data): self;
    public function setError(string $error): self;
    public function setContentType(string $content_type): self;
    public function setOrigin(string $origin): self;
    /**
     * @param string[] $methods
     */
    public function setMethods(array $methods): self;
    public function setAge(int $age): self;
    public function send(): void;
    public static function getInstance(): self;
}


/**
 * Class Response
 * 
 * This singleton is responsible for sending the response to the client
 * - **getInstance**: returns the instance of the class
 * - **setCode**: sets the response code
 * - **setMessage**: sets the message in the payload
 * - **setPayload**: sets the data in the payload
 * - **setError**: sets the error in the payload
 * - **setContentType**: sets the content type in the header
 * - **setOrigin**: sets the origin in the header
 * - **setMethods**: sets the methods in the header
 * - **setAge**: sets the age in the header
 * - **send**: sends the response to the client
 * 
 * @phpstan-type PayloadType array{message: string, data: mixed, error: string} | array{}
 * @phpstan-type ConfigKeys key-of<ConfigType>
 * @phpstan-type ConfigType array{code: int, message: string, data: PayloadType, error: string, content_type: string, origin: string, methods: string[], age: int, location?: string}
 * @phpstan-type CookieType array{string, string, array{expires: int, path: string, domain: string, secure: bool, httponly: bool}} | array{}
 */
class Response extends AbsResponse implements IResponse
{
    private static ?Response $instance = null;

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param ConfigType $config
     */
    public function fromConfig(array $config): self
    {
        $this->config = array_merge($this->config, $config);
        $setters = [
            'code' => 'setCode',
            'message' => 'setMessage',
            'data' => 'setPayload',
            'error' => 'setError',
            'content_type' => 'setContentType',
            'origin' => 'setOrigin',
            'methods' => 'setMethods',
            'age' => 'setAge',
            'location' => 'setLocation'
        ];

        foreach ($setters as $key => $setter) {
            if (isset($config[$key])) {
                $this->$setter($config[$key]);
            }
        }

        return $this;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @param CookieType $cookies
     */
    public function addCookies(array $cookies): self
    {
        $this->cookies[] = $cookies;
        return $this;
    }

    public function setMessage(string $message): self
    {
        $this->payload = array_merge($this->payload, ["message" => $message]);
        return $this;
    }

    public function setPayload(mixed $data): self
    {
        $this->payload = array_merge($this->payload, ["data" => $data]);
        return $this;
    }

    public function setError(string $error): self
    {
        $this->payload = array_merge($this->payload, ["error" => $error]);
        return $this;
    }

    public function setContentType(string $content_type): self
    {
        $this->header["Content-Type: "] = $content_type;
        return $this;
    }

    public function setOrigin(string $origin): self
    {
        $this->header["Access-Control-Allow-Origin: "] = $origin;
        return $this;
    }

    public function setMethods(array $methods): self
    {
        $this->header["Access-Control-Allow-Methods: "] = self::methodsToString($methods);
        return $this;
    }

    public function setAge(int $age): self
    {
        $this->header["Access-Control-Age: "] = $age;
        return $this;
    }

    public function setLocation(string $location): self
    {
        $this->header["Location: "] = $location;
        return $this;
    }

    private function applyCookies(): void
    {
        if (!empty($this->cookies)) {
            foreach ($this->cookies as $cookie) {
                if (isset($cookie[0], $cookie[1], $cookie[2])) {
                    [$name, $value, $options] = $cookie;
                    setcookie($name, $value, $options);
                }
            }
        }
    }

    private function applyHeaders(): void
    {
        foreach ($this->header as $key => $value) {
            header($key . $value);
        }
    }

    public function send(): void
    {
        $this->applyCookies();
        $this->applyHeaders();
        http_response_code($this->code ?? 200);
        echo json_encode($this->payload);
    }
}
