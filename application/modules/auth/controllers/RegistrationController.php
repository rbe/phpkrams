<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 *
 */
class Auth_RegistrationController extends ZendControllerBase {

    public function init() {
        parent::init();
    }

    public function preDispatch() {
        parent::preDispatch();
    }

    public function postDispatch() {
        parent::postDispatch();
    }

    /**
     * Check user activation request.
     */
    public function checkAction() {
        $ok = true;
        //
        $request = $this->getRequest();
        // Get User instance and populate with data
        $registerUser = ZendHelper::getSessionNs()->register_user;
        $registerUser->setSalutation($request->getParam("salutation"));
        $registerUser->setTitle($request->getParam("title"));
        $registerUser->setFirstname($request->getParam("firstname"));
        $registerUser->setLastname($request->getParam("lastname"));
        $registerUser->setStreet($request->getParam("street"));
        $registerUser->setZip($request->getParam("zip"));
        $registerUser->setCity($request->getParam("city"));
        $registerUser->setTelephone($request->getParam("telephone"));
        $registerUser->setEmail($request->getParam("email"));
        $registerUser->setHomepage($request->getParam("homepage"));
        $registerUser->setUsername($request->getParam("email"));
        // Check captcha
        if (!$this->validateImageCaptcha($this->getRequest()->getParam("captcha"))) {
            $this->addErrorMessage("Bitte Captcha prüfen!<br/>");
            $ok = false;
        }
        // Check required values
        if (!$registerUser->getLastname() || !$registerUser->getEmail()) {
            $this->addErrorMessage("Bitte die Pflichtfelder ausfüllen (mit * gekennzeichnet)!<br/>");
            $ok = false;
        }
        // Check email
        if (!ZendHelper::validateEmail($registerUser->getEmail())) {
            $this->addErrorMessage("Bitte die Email-Adresse prüfen!<br/>");
            $ok = false;
        }
        // Check passwords
        $p1 = $request->getParam("p1", NULL);
        $p2 = $request->getParam("p2", NULL);
        if ($p1 && $p2) {
            if ($p1 != $p2) {
                $this->addErrorMessage("Bitte die Passwörter prüfen!<br/>");
                $ok = false;
            } else {
                $registerUser->setPassword($p1);
            }
        } else {
            $this->addErrorMessage("Bitte die Passwörter prüfen!<br/>");
            $ok = false;
        }
        // Forward
        if ($ok) {
            // Generate unique ID for activation mail
            $authUrlId = IdHelper::generate();
            // Persist user in database
            $authUser = new AuthUser();
            $authUser->insert(
                array(
                    "username" => $registerUser->getUsername(),
                    "password" => $registerUser->getPassword(),
                    "salutation" => $registerUser->getSalutation(),
                    "title" => $registerUser->getTitle(),
                    "firstname" => $registerUser->getFirstname(),
                    "lastname" => $registerUser->getLastname(),
                    "street" => $registerUser->getStreet(),
                    "zip" => $registerUser->getZip(),
                    "city" => $registerUser->getCity(),
                    "telephone" => $registerUser->getTelephone(),
                    "email" => $registerUser->getEmail(),
                    "homepage" => $registerUser->getHomepage(),
                    "auth_url_id" => $authUrlId
                )
            );
            // Everything's OK
            $this->forward("sendmail", "registration", "auth",
                array(
                    "email" => $registerUser->getEmail(),
                    "firstname" => $registerUser->getFirstname(),
                    "lastname" => $registerUser->getLastname(),
                    "auth_url_id" => $authUrlId
                )
            );
        } else {
            // Please check values...
            $this->forwardNoDispatch("register");
        }
    }

    /**
     *
     */
    public function sendmailAction() {
        // Get User instance
        $registerUser = ZendHelper::getSessionNs()->register_user;
        //
        $request = $this->getRequest();
        $email = $request->getParam("email");
        $firstname = $request->getParam("firstname");
        $lastname = $request->getParam("lastname");
        $authUrlId = $request->getParam("auth_url_id");
        // Send email
        try {
            $client = new Zend_Http_Client("http://" . ZendHelper::getConfig()->host . "/email/send/do");
            $client->setConfig(
                array(
                    'timeout' => 30
                )
            );
            $client->setParameterGet(
                array(
                    "to" => $email,
                    "tpl" => "oneshot",
                    "activationlink" => "http://" . ZendHelper::getConfig()->host . "/auth/registration/activate/id/" . $authUrlId
                )
            );
            $response = $client->request();
            if ($response->getStatus() != 200) {
                throw new IllegalStateException("Could not send email: " . $response->getMessage());
            }
            //
            $this->view->result = true;
        } catch (Exception $e) {
            $this->addErrorMessage($e->getMessage());
            $ok = false;
        }
    }

    /**
     * Activate user account.
     */
    public function activateAction() {
        // Get auth url ID from request
        $authUrlId = $this->getRequest()->getParam("id");
        // Persist user in database
        $authUser = new AuthUser();
        $result = $authUser->findByAuthId($authUrlId);
        if ($result->count()) {
            // Update table with auth information
            $authUser->update(array(
                    "auth_url_clicked_on" => date("Y-m-d H:i:s"),
                    "auth_url_id" => NULL
                ),
                "auth_url_id = '" . $authUrlId . "'"
            );
            //
            ZendHelper::removeUser();
            // Forward
            $this->forwardToAuthModule(array(
                    "u" => $user->getUsername()
                )
            );
        } else {
            // Forward to auth module
            $this->forwardToAuthModule();
        }
    }

    /**
     *
     */
    public function registerAction() {
        // Create instance of image captcha
        $captcha = $this->generateImageCaptcha();
        // Create fresh User instance
        $registerUser = ZendHelper::getSessionNs()->register_user;
        if (!$registerUser) {
            $registerUser = new User();
            ZendHelper::getSessionNs()->register_user = $registerUser;
        }
        // Save register_user object in view
        $this->view->register_user = $registerUser;
    }

}

?>
