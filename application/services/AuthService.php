<?php

namespace App\Services;

use \Firebase\JWT\JWT;
use \Phalcon\Http\Request;

class AuthService extends \Phalcon\DI\Injectable
{

    const JWT_ALG = 'HS256';
    private static $_secondsValid = 60 * 60;

    public function authenticate(array $userData)
    {
        $jwt = $this->_generateJWT($userData);
        return $jwt;
    }

    /**
     * Generate JWT token based on given user-data
     * 
     * @param array $userData
     */
    private function _generateJWT(array $userData)
    {        
        $issuedAt = time();
        $secondsValid = self::$_secondsValid;
        $expirationTime = $issuedAt + $secondsValid; // jwt valid for $secondsValid seconds from the issued time
        $payload = array(
            'sub' => $userData['email'],
            'email' => $userData['email'],
            'userRole' => 'user', // or admin
            'iat' => $issuedAt,
            'exp' => $expirationTime
        );
        $key = $this->config->jwtSecretKey;
        $alg = self::JWT_ALG;
        $jwt = JWT::encode($payload, $key, $alg);
        
        return $jwt;
    }

    /**
     * Check if request is authenticated
     *      
     * @return boolean true if is authenticated, false otherwise
     */
    public function isAuthenticated()
    {
        $token = $this->_getBearerToken();        
        
        $decoded_array = $this->_validateJWT($token);
        if (!empty($decoded_array)) {
            // process valid token
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Get decoded authentication token for the currently authenticated user
     * 
     * @return mixed array of decoded jwt claims or false
     */
    public function getDecodedAuthToken()
    {
        $token = $this->_getBearerToken();                
        $decoded_array = $this->_validateJWT($token);                
        if (!empty($decoded_array)) {
            return $decoded_array;
        } else {
            return false;
        }
    }

    /**
     * Get access token from header
     * 
     * @return authorization request header information (if exists) or null
     */
    private function _getBearerToken()
    {
        $request = new Request();
        $authHeader = $request->getHeader('Authorization');
        if (strpos($authHeader, "Bearer ") !== false) {
            $token = explode(" ", $authHeader);
            if (isset($token[1])) {
                return $token[1]; // actual token
            }
        }

        return null;
    }

    /**
     * Check if jwt token is valid     
     * 
     * @param type $token
     * @return array decoded array if is-valid-wt, null otherwise
     */
    private function _validateJWT($token)
    {
        try {
            $key = $this->config->jwtSecretKey;
            JWT::$leeway = 60; // $leeway in seconds
            $decoded = JWT::decode($token, $key, array(self::JWT_ALG));
            $decoded_array = (array) $decoded;            
        } catch (\Exception $e) {
            $decoded_array = null;
        }

        return $decoded_array;
    }
}
