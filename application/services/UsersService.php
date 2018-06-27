<?php

namespace App\Services;

use App\Collections\Users;

/**
 * Business logic for users  
 */
class UsersService
{

    /**
     * Currently a STUB method to create only 1 user to demo REST API capabilities
     * 
     * @param array $userData
     */
    public function createUser(array $userData = [])
    {        
        $users = Users::find();
        if (count($users)) { // test user is already created
            echo 'User exists already. If you want, use "db.users.drop()" to clean up the table from mongo console.';            
            return;
        }

        $user = new Users();

        $user->email = $userData['email'];        
        $user->password = password_hash($userData['password'], PASSWORD_DEFAULT);

        if ($user->save() === false) {            
            return $messages = $user->getMessages();
        } else {
            return true;
        }
    }

    /**
     * Get user by given email and password
     * 
     * @param array $userData
     */
    public function getUser(array $userData)
    {                
        $user = Users::findFirst([
            [
                'email' => $userData['email']
            ]
        ]);                
        
        if ($user) {
            if (password_verify($userData['password'], $user->password)) {
                return $user;
            } else {
                return null;
            }
        }
    }
}
