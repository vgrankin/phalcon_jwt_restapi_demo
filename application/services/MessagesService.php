<?php

namespace App\Services;

use App\Collections\Messages;

/**
 * Business logic for messages
 */
class MessagesService
{

    /**
     * @param array $messageData
     */
    public function createMessage(array $messageData = [])
    {
        $messages = new Messages();

        $messages->user_email = $messageData['email'];
        $messages->message = $messageData['message'];
        $messages->date_created = date('Y-m-d H:i:s');
        
        if ($messages->save() === false) {
            return $messages->getMessages();
        } else {            
            return true;
        }
    }

    public function getUserMessages($userEmail)
    {       
        $messages = [];
        
        $result = Messages::find([['user_email' => $userEmail]]);
        foreach ($result as $message) {
            $messages[] = [
                'date_created' => $message->date_created,
                'message' => $message->message
            ];
        }
        
        return $messages;
    }
}
