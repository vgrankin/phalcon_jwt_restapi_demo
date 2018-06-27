<?php

namespace Test;

use Phalcon\Di;
use Phalcon\Test\UnitTestCase as PhalconTestCase;

abstract class UnitTestCase extends PhalconTestCase
{    
    protected $_http;

    public function setUp()
    {        
        parent::setUp();        
        
        $this->_http = new \GuzzleHttp\Client(['base_uri' => 'http://phalcon.api/api/']);
        
        // Load any additional services that might be required during testing
        $di = Di::getDefault();

        // Get any DI components here. If you have a config, be sure to pass it to the parent

        $this->setDi($di);
    }
}