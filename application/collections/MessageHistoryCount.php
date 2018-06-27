<?php

namespace App\Collections;

use Phalcon\Mvc\MongoCollection;

class MessageHistoryCount extends MongoCollection
{
    public function initialize()
    {
        $this->setSource('message_history_count');
    }
}