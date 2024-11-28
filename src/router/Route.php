<?php

namespace App\Router;


require_once BASE_DIR . '/vendor/autoload.php';

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Route
{
    public function __construct(
        public string $path,
    ) {}
}
