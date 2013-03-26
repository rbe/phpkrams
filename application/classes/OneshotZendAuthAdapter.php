<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

class OneshotZendAuthAdapter implements Zend_Auth_Adapter_Interface {

    /**
     *
     * @var string
     */
    private $username;

    /**
     * Constructor.
     * @param string $username
     */
    public function OneshotZendAuthAdapter($username) {
        $this->username = $username;
    }

    /**
     * Performs an authentication attempt.
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticate() {
        $authResult = NULL;
        // Query database
        $oneshotUser = new OneshotUser();
        $result = $oneshotUser->findByEmail($this->username);
        // If we got a row
        if ($result->count()) {
            try {
                $row = $result->current();
                // Create User instance
                $user = new User($row->email);
                $user->setLastname($row->name);
                $user->setEmail($row->email);
                // Create auth result
                $authResult = new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user);
            } catch (Exception $e) {
                // Create auth result
                $authResult = new Zend_Auth_Result(Zend_Auth_Result::FAILURE, NULL);
            }
        } else {
            // Create auth result
            $authResult = new Zend_Auth_Result(Zend_Auth_Result::FAILURE, NULL);
        }
        return $authResult;
    }

}

?>
