<?php

use App\Lib\Router\Routing;

require_once __DIR__ . "/vendor/autoload.php";    // Require the constants
require_once __DIR__ . "/src/lib/bootstrap.php";    // Require the bootstrap
Routing::start();                                   // Start the application