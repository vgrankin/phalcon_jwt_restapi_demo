<?php

// output errors to browser
ini_set('display_errors', 1);
// report all errors
error_reporting(E_ALL);
// convert warnings/notices to exceptions
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    throw new Exception($errstr . PHP_EOL . $errfile . ":" . $errline, $errno);
});

function out($content = '')
{
    echo '<pre>';
    print_r($content);
    echo '</pre>';
    die();
}

use Phalcon\Mvc\Micro;
use Phalcon\Http\Response;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH);

$loader = require(APP_PATH . '/config/loader.php');

// Load config
$config = require(APP_PATH . '/config/config.php');

/**
 * Prepare/init dependency injection container
 *  
 * @var \Phalcon\DI\FactoryDefault $di
 */
$di = require APP_PATH . '/config/di.php';

// Handle app
// Create and bind the DI to the application
$app = new Micro($di);

/**
 * hook: Executed before every route is executed
 */
$app->before(function () use ($app, $di) {

    $authService = $di->get('authService');

    // first check some allowed routes for which authentication is not required
    $allowedRoutes = ['/api/authenticate', '/api/create_user', '/api/get_user', '/api/auth_fail'];
    $currentRouteName = $di->get('router')->getMatchedRoute()->getCompiledPattern();
    foreach ($allowedRoutes as $allowedRoute) {
        if ($currentRouteName == $allowedRoute) {
            return true;
        }
    }

    if (!$authService->isAuthenticated()) {
        // process error (output some authentication-related json response here)
        $app->stop();

        $app->response->redirect("/api/auth_fail")->sendHeaders();

        return false;
    }

    return true;
});

/**
 * hook: Convert all controller outputs to json and send response
 */
//$app->after(function () use ($app) {
//    // Getting the return value of method
//    $return = $app->getReturnedValue();
//    
//    if (is_array($return)) {
//        // Transforming arrays to JSON
//        $app->response->setJsonContent($return);
//    } elseif (!strlen($return)) {
//        // Successful response without any content
//        $app->response->setStatusCode('204', 'No Content');
//    } else {
//        // Unexpected response
//        throw new Exception('Bad Response');
//    }
//
//    // Sending response to the client
//    $app->response->send();    
//});

/**
 * Create test/dummy user to work with
 * 
 * username: phalconuser@phalconapi.com
 * password: test123
 */
$app->get('/api/get_user', function () use ($config, $di) {
    $usersService = $di->get('usersService');
    $user = $usersService->getUser([
        'email' => 'phalconuser@phalconapi.com',
        'password' => 'test123'
    ]);

    $response = new Response();
    $response->setJsonContent($user);
    return $response;
});

/**
 * Will only create user (with given credentials) once (if not exists already)
 */
$app->get('/api/create_user', function () use ($config, $di) {
    $usersService = $di->get('usersService');
    $usersService->createUser([
        'email' => 'phalconuser@phalconapi.com',
        'password' => 'test123'
    ]);
});

/**
 * Add new message to the database
 */
$app->post('/api/create_message', function () use ($config, $di) {

    $error = false;
    $message = $this->request->getPost("message");
    if ($message) {
        $authService = $di->get('authService');
        $token = $authService->getDecodedAuthToken();
        if ($token) {
            $messagesService = new App\Services\MessagesService;
            $success = $messagesService->createMessage([
                'email' => $token['email'],
                'message' => $message
            ]);
            if ($success === true) {
                $response = new Response();
                $response->setJsonContent([
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Message is successfully saved'
                ]);
                return $response;                
            } else {
                // process error messages from messages-service
                $error = true;
            }
        } else {
            $error = true;
        }
    } else {
        $error = true;
    }

    if ($error) {
        // process error, return json error-response
        $response = new Response();
                $response->setJsonContent([
            'code' => 0,
            'status' => 'error',
            'message' => 'Message save fail'
        ]);
        return $response;          
    }
});

/**
 * Get user messages
 */
$app->get('/api/message_history', function () use ($config, $di) {
    
    $error = false;        
    
    $authService = $di->get('authService');
    $token = $authService->getDecodedAuthToken();
    if ($token) {
        
        $messageHistoryCount = new App\Services\MessageHistoryCountService;
        $cnt = $messageHistoryCount->incrementApiAccessCnt();     
        
        $messagesService = new App\Services\MessagesService;
        $message_hitory = $messagesService->getUserMessages($token['email']);
        
        $response = new Response();
        $response->setJsonContent([
            'code' => 200,
            'status' => 'success',
            'message' => 'Messages are successfully retrieved',
            'data' => [
                'retrievalCnt' => $cnt,
                'message_history' => $message_hitory
            ]
        ]);
        return $response;
    } else {
        $error = true;
    }

    if ($error) {
        // process error, return appropriate json error-response        
    }
});

/**
 * To test authentication, run this request via Postman or equivalent:
 *  * don't forget to run http://phalcon.api/create_user GET request first to create user in the database
 *  - use "x-www-form-urlencoded" method
 *  - add POST key-value pair: email -> phalconuser@phalconapi.com
 *  - add POST key-value pair: password -> test123
 *  - execute POST request to the server 
 *  - use token in response to access auth-only areas of the api
 */
$app->post('/api/authenticate', function () use ($config, $di) {

    $email = $this->request->getPost("email");
    $password = $this->request->getPost("password");

    $usersService = new App\Services\UsersService;
    $user = $usersService->getUser([
        'email' => $email,
        'password' => $password
    ]);
    if ($user) {

        $authService = new App\Services\AuthService();
        $jwt = $authService->authenticate([
            'email' => $user->email
        ]);

        // stub: just echoing, store this on user's device
        $arr = ["auth_token" => $jwt];
        $response = new Response();
        $response->setJsonContent($arr);
        return $response;
    } else {
        $result = [
            'code' => 0,
            'status' => 'error',
            'message' => 'Invalid credentials - Authentication process failed!'
        ];
        $response = new Response();
        $response->setJsonContent($result);
        return $response;
    }
});

$app->get('/api/auth_test', function () use ($config, $di) {
    $result = [
        'code' => 200,
        'status' => 'success',
        'message' => 'Route only authenticated users has access to!'
    ];

    $response = new Response();
    $response->setJsonContent($result);
    return $response;
});

$app->get('/api/auth_fail', function () use ($config, $di) {
    $result = [
        'code' => 0,
        'status' => 'error',
        'message' => 'Invalid JWT - Authentication failed!'
    ];
    $response = new Response();
    $response->setJsonContent($result);
    return $response;
});

// Processing request
$app->handle();
