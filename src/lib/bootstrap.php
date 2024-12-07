<?php

use DI\ContainerBuilder;

/**
 * @global string BASE_DIR The base directory of the project
 */
define("BASE_DIR", dirname(dirname(__DIR__))); // 2 levels up

$builder = new ContainerBuilder();
$builder->addDefinitions('config/definitions.php');
$container = $builder->build();

/**
 * @global Container $container The DI container
 */
define('CONTAINER', $container);




require_once BASE_DIR . '/src/lib/debug/utils.php';          // Require the debug utilities