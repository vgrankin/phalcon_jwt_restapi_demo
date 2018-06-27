<?php

namespace App\Services;

use App\Collections\MessageHistoryCount;

/**
 * Business logic for users  
 */
class MessageHistoryCountService
{
    public function incrementApiAccessCnt()
    { 
        $document = MessageHistoryCount::findFirst();
        if ($document) {
            $document->count = $document->count + 1;
        } else {            
            $document = new MessageHistoryCount();
            $document->count = 1;
        }
        
        if ($document->save() === false) {
            return false;
        } else {
            return $document->count;
        }
    }

}
