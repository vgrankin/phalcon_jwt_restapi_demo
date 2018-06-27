<?php

return new \Phalcon\Config([
    'jwtSecretKey' => 'my_secret_key',
    'mongodb' => [
        'host' => '172.19.0.2',
        'port' => 27017,
        'database' => 'users'
    ]
]);
