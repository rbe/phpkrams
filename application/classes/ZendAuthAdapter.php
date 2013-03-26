<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

class ZendAuthAdapter implements Zend_Auth_Adapter_Interface {

    /**
     *
     * @var string
     */
    private $username;

    /**
     *
     * @var string
     */
    private $password;

    /**
     * Constructor.
     * @param string $username
     * @param string $password
     */
    public function ZendAuthAdapter($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Performs an authentication attempt.
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticate() {
        $authResult = NULL;
        // Query database
        $authUser = new AuthUser();
        $result = $authUser->findAuthenticatedUser($this->username, $this->password);
        // If we got a row
        if ($result->count()) {
            try {
                $row = $result->current();
                // Create User instance
                $user = new User($row->username);
                $user->setUid($row->uid);
                $user->setSalutation($row->salutation);
                $user->setTitle($row->title);
                $user->setFirstname($row->firstname);
                $user->setLastname($row->lastname);
                $user->setStreet($row->street);
                $user->setZip($row->zip);
                $user->setCity($row->city);
                $user->setTelephone($row->telephone);
                $user->setEmail($row->email);
                $user->setHomepage($row->homepage);
                $user->setPassword($row->password);
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
