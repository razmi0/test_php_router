<?php

namespace App\Lib;

use App\Lib\Interfaces\IController;

abstract class Controller implements IController
{
    public function __construct() {}

    protected static function createJsClosure(string $js): string
    {
        return trim("() => {$js}");
    }
}
