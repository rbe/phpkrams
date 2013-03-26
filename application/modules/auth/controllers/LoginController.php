<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 *
 */
class Auth_LoginController extends ZendControllerBase {

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
     * Perform login.
     */
    public function doAction() {
        $ok = true;
        // Remove user from session
        ZendHelper::removeUser();
        // Check captcha
        if (!$this->validateImageCaptcha($this->getRequest()->getParam("captcha"))) {
            $this->addErrorMessage("Bitte Captcha prüfen!<br/>");
            $ok = false;
        }
        // Check data
        $username = $this->getRequest()->getParam("u");
        $password = $this->getRequest()->getParam("p");
        if (!$username || !$password) {
            $em .= "Kein Benutzername oder Passwort eingegeben!<br/>";
            $ok = false;
        }
        // Check authentication
        if ($ok) {
            // Set up the authentication adapter
            // Attempt authentication, saving the result
            $result = ZendHelper::getAuth()->authenticate(new ZendAuthAdapter($username, $password));
            // Process result of auth login attempt
            switch ($result->getCode()) {
                case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                    $this->addErrorMessage("Fehler bei der Authentifizierung!<br/>");
                    $ok = false;
                    break;
                case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                    $this->addErrorMessage("Fehler bei der Authentifizierung!<br/>");
                    $ok = false;
                    break;
                case Zend_Auth_Result::FAILURE:
                    $this->addErrorMessage("Fehler bei der Authentifizierung!<br/>");
                    $ok = false;
                    break;
                case Zend_Auth_Result::SUCCESS:
                    // Save user in session
                    ZendHelper::setUser($result->getIdentity());
                    break;
                default:
                    $this->addErrorMessage("Unbekannter Fehler!<br/>");
                    $ok = false;
                    break;
            }
        }
        // Forward
        if ($ok) {
            // Redirect
            $originalRequest = ZendHelper::getOriginalRequest();
            if ($originalRequest && count($originalRequest) == 4) {
                $this->_forward($originalRequest['action'],
                    $originalRequest['controller'],
                    $originalRequest['module'],
                    $originalRequest['params']);
            }
        } else {
            // Forward to auth module
            $this->forwardToAuthModule(array("u" => $username));
        }
    }

    /**
     * Dummy action. Used when reload button is hit; renders view from 'do' action.
     */
    public function indexAction() {
        $this->render("do");
    }

}

?>
