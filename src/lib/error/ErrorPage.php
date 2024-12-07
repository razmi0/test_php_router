<?php


namespace App\Lib\HTTP;

use Exception;

interface IErrorPage
{
  /**
   * Handle HTTP 404 errors.
   *
   * @param string $uri The URI that caused the 404 error.
   */
  public static function HTTP404(string $uri): void;

  /**
   * Handle HTTP 500 errors.
   *
   * @param Exception|null $exception The exception that caused the error, if available.
   * @param string|null $message A custom error message, if any.
   * @param array<mixed>|null $data Additional data to send to the client.
   * @param string|null $file The file in which the error occurred.
   * @param int|null $line The line number where the error occurred.
   * @param int|null $code The error code, if any.
   * @param string|null $trace The error trace, if any.
   */
  public static function HTTP500(
    ?Exception $exception = null,
    ?string $message = null,
    ?array $data = null,
    ?string $file = null,
    ?int $line = null,
    ?int $code = null,
    ?string $trace = null
  ): void;
}



class ErrorPage implements IErrorPage
{
  public static function HTTP404(string $uri): void
  {
    header("HTTP/1.0 404 Not Found");
    http_response_code(404);
    echo
    <<<HTML
                <link rel="stylesheet" href="/error.css" preload>
                <div class="container">
                    <h1>404 Not Found</h1>
                    <p class="msg">The requested URL was not found on this server.</p>
                    <p class="uri">URI: {$uri}</p>
                </div>
            HTML;
    exit();
  }


  public static function HTTP500(
    ?Exception $exception = null,
    ?string $message = null,
    ?array $data = null,
    ?string $file = null,
    ?int $line = null,
    ?int $code = null,
    ?string $trace = null
  ): void {
    header("HTTP/1.0 500 Internal Server Error");
    http_response_code(500);
    if ($exception) {
      $fileParts = explode('/', $exception->getFile());
      $filename = array_pop($fileParts);
      echo
      <<<HTML
            <link rel="stylesheet" href="/error.css" preload>
            <div class="container">
                <h1>500 Internal Server Error</h1>
                <p class="code">code: {$exception->getCode()}</p><p class="msg">{$exception->getMessage()}</p>
                <p class="file">{$filename} Line : {$exception->getLine()}</p>
                <p>Trace   : {$exception->getTraceAsString()}</p>
                <small>{$exception->__toString()}</small>
            </div>
        HTML;
    } else {
      $json = json_encode($data);
      echo
      <<<HTML
                <link rel="stylesheet" href="/error.css" preload>
                <div class="container">
                    <h1>500 Internal Server Error</h1>
                    <p class="msg">{$message}</p>
                    <pre class="data">{$json}</pre>
                    <p class="file">{$file} Line : {$line}</p>
                    <p class="code">code : {$code}</p>
                    <p>Trace   : {$trace}</p>
                </div>
            HTML;
    }
    exit();
  }
}
