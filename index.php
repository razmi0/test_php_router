<?php

use DI\ContainerBuilder;

require_once 'vendor/autoload.php';
require_once 'src/lib/constants.php';

$builder = new ContainerBuilder();
$builder->addDefinitions('config/definitions.php');
$container = $builder->build();

// Router
require_once BASE_DIR . '/src/lib/router/starter.php';
