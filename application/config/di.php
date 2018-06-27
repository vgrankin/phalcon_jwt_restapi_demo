<?php

use Phalcon\Di\FactoryDefault;

// Initializing a DI Container
$di = new FactoryDefault();

// application-wide config
$di->setShared('config', $config);

/**
 * Overriding Response-object to set the Content-type header globally
 */
$di->setShared('response', function () {
    $response = new \Phalcon\Http\Response();
    $response->setContentType('application/json', 'UTF-8');

    return $response;
});

// this manager is required by mongo
$di->set("collectionManager", function () {
    return new \Phalcon\Mvc\Collection\Manager();
});

// Simple database connection to localhost
$di->set('mongo', function () use ($config) {
    $config = $config->mongodb;    
    $mongoClient = new \Phalcon\Db\Adapter\MongoDB\Client("mongodb://{$config->host}:{$config->port}");
    $mongodb = $mongoClient->selectDatabase($config->database);
    return $mongodb;
}, true);

//Service to perform CRUD operations with the Users
$di->setShared('usersService', 'App\Services\UsersService');
//Service to perform CRUD operations with the Messages
$di->setShared('messagesService', 'App\Services\MessagesService');
$di->setShared('messageHistoryCount', 'App\Services\MessageHistoryCountService');
// Service to perform authentication-related ops
$di->setShared('authService', 'App\Services\AuthService');

return $di;