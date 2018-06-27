<?php

namespace App\Collections;

use Phalcon\Mvc\MongoCollection;

class Users extends MongoCollection
{
    public function initialize()
    {
        $this->setSource('users');
    }
}