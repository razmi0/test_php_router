<?php

namespace App\Lib;

use App\Lib\Interfaces\IController;
use App\Lib\HTTP\Request;
use App\Lib\HTTP\Response;

abstract class Controller implements IController
{
    public function __construct(protected Request $request, protected Response $response) {}

    public static function js(string $js): string
    {
        return trim("
        <script>
        (() => { 
        $js 
        })();
        </script>
        ");
    }
}
