<?php


namespace App\Lib\HTTP;


require_once BASE_DIR . "/vendor/autoload.php";

/**
 * @phpstan-type PayloadType array<string, mixed>
 */
interface IError
{
    /**
     * @param PayloadType $payload
     */
    public static function HTTP404(string $msg, array $payload): void; // 404 Not found
    /**
     * 
     * @param PayloadType $payload
     */
    public static function HTTP405(string $msg, array $payload): void; // 405 Method not allowed
    /**
     * @param PayloadType $payload
     */
    public static function HTTP400(string $msg, array $payload): void; // 400 Bad request
    /**
     * @param PayloadType $payload
     */
    public static function HTTP401(string $msg, array $payload): void; // 401 Unauthorized
    /**
     * @param PayloadType $payload
     */
    public static function HTTP415(string $msg, array $payload): void; // 415 Unsupported Media Type
    /**
     * @param PayloadType $payload
     */
    public static function HTTP500(string $msg, array $payload): void; // 500 Internal server error
    /**
     * @param PayloadType $payload
     */
    public static function HTTP204(string $msg, array $payload): void; // 204 No content
    /**
     * @param PayloadType $payload
     */
    public static function HTTP503(string $msg, array $payload): void; // 503 Service unavailable
}

/**
 * Error class
 * 
 * Send an error response to the client
 * @static **HTTP404**: Not found
 * @static **HTTP405**: Method not allowed
 * @static **HTTP400**: Bad request
 * @static **HTTP401**: Unauthorized
 * @static **HTTP415**: Unsupported Media Type
 * @static **HTTP500**: Internal server error
 * @static **HTTP204**: No content
 * @static **HTTP503**: Service unavailable
 * @phpstan-type PayloadType array<string, mixed>
 * @phpstan-type ErrorSummary array{message: string, error: string, payload: PayloadType} | array{}
 */
class Error implements IError
{
    private int $code = 0;
    private string $message = "";
    private string $error = "";
    /**
     * @var ErrorSummary
     */
    private array $summary = [];

    private function __construct() {}

    private function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    private function setCode(int $code): self
    {
        $this->code = $code;
        return $this;
    }

    private function setError(string $error): self
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @param PayloadType $data
     */
    private function setSummary(array $data): self
    {
        $this->summary = [
            "message" => $this->message,
            "error" => $this->error,
            "payload" => $data
        ];
        return $this;
    }

    private function send(): void
    {
        header("Content-Type: application/json; charset=UTF-8");
        http_response_code($this->code);
        echo json_encode($this->summary, JSON_PRETTY_PRINT);
        exit();
    }

    public static function HTTP404(string $msg, array $payload = []): void
    {
        (new self())->setCode(404)
            ->setMessage($msg)
            ->setError("Ressource non trouvée")
            ->setSummary($payload)
            ->send();
    }

    public static function HTTP405(string $msg, array $payload = []): void
    {
        (new self())->setCode(405)
            ->setMessage($msg)
            ->setError("Méthode non autorisée")
            ->setSummary($payload)
            ->send();
    }

    public static function HTTP400(string $msg, array $payload = []): void
    {
        (new self())->setCode(400)
            ->setMessage($msg)
            ->setError("Requête invalide")
            ->setSummary($payload)
            ->send();
    }

    public static function HTTP401(string $msg, array $payload = []): void
    {
        (new self())->setCode(401)
            ->setMessage($msg)
            ->setError("Non autorisé")
            ->setSummary($payload)
            ->send();
    }

    public static function HTTP415(string $msg, array $payload = []): void
    {
        (new self())->setCode(415)
            ->setMessage($msg)
            ->setError("Type de média non supporté")
            ->setSummary($payload)
            ->send();
    }

    public static function HTTP500(string $msg, array $payload = []): void
    {
        (new self())->setCode(500)
            ->setMessage($msg)
            ->setError("Erreur interne")
            ->setSummary($payload)
            ->send();
    }

    public static function HTTP204(string $msg, array $payload = []): void
    {
        (new self())->setCode(204)
            ->setMessage($msg)
            ->setError("Aucun contenu")
            ->setSummary($payload)
            ->send();
    }

    public static function HTTP503(string $msg, array $payload = []): void
    {
        (new self())->setCode(503)
            ->setMessage($msg)
            ->setError("Service non disponible")
            ->setSummary($payload)
            ->send();
    }
}
