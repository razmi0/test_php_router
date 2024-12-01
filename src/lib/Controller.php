<?php

namespace App\Lib;

use App\Lib\Interfaces\IController;

abstract class Controller implements IController
{
    public function __construct() {}
}
