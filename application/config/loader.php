<?php

use \Phalcon\Loader;

/**
 * Include composer autoloader
 */
require APP_PATH . "/vendor/autoload.php";

// Use Loader() to autoload our model
$loader = new Phalcon\Loader();
$loader->registerNamespaces([
    'App\Collections' => APP_PATH . '/collections/',
    'App\Services' => APP_PATH . '/services/',
    'Phalcon' => APP_PATH . '/Library/Phalcon/'    
]);
$loader->register();

return $loader;