<?php

use Phalcon\Di;
use Phalcon\Di\FactoryDefault;

ini_set("display_errors", 1);
error_reporting(E_ALL);

function out($content = '')
{
    echo '<pre>';
    print_r($content);
    echo '</pre>';
    die();
}

define("ROOT_PATH", __DIR__);
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH);

set_include_path(
    ROOT_PATH . PATH_SEPARATOR . get_include_path()
);

include __DIR__ . "/../config/loader.php";

$namespaces = $loader->getNamespaces();
$namespaces['Test'] = APP_PATH . '/tests/Test';
$loader->registerNamespaces($namespaces);
$loader->register();

$config = include __DIR__ . "/../config/config.php";
$di = include __DIR__ . "/../config/di.php";