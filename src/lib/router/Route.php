<?php

namespace App\Lib\Routing;

require_once BASE_DIR . '/vendor/autoload.php';

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(
        public string $path,
        public string|null $view = null
    ) {}
}
