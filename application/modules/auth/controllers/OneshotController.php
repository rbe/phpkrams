<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 * Authenticate session one time via Email.
 * @author rbe
 */
class Auth_OneshotController extends ZendControllerBase {

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
        $rq = new ZendRq($this->getRequest());
        $email = $rq->email;
        $name = $rq->name;
        // Check captcha
        if (!$this->validateImageCaptcha($rq->captcha)) {
            $this->addErrorMessage("Bitte Captcha prüfen!<br/>");
            $ok = false;
        }
        // Check required values
        if (!$name || !$email) {
            $this->addErrorMessage("Bitte die Pflichtfelder ausfüllen (mit * gekennzeichnet)!<br/>");
            $ok = false;
        } elseif (!ZendHelper::validateEmail($email)) {
            // Check email
            $this->addErrorMessage("Bitte die Email-Adresse prüfen!<br/>");
            $ok = false;
        }
        // Generate unique ID for activation mail
        $authUrlId = IdHelper::generate();
        //
        $a = array(
            "email" => $email,
            "name" => $name,
            "auth_url_id" => $authUrlId
        );
        // Forward
        if ($ok) {
            // Original request
            $originalRequest = ZendHelper::getOriginalRequest();
            // Persist user in database
            $oneshotUser = new OneshotUser();
            $result = $oneshotUser->findByEmail($email);
            if ($result->count()) {
                $oneshotUser->update(
                    array(
                        "name" => $name,
                        "auth_url_id" => $a["auth_url_id"],
                        "zendaction" => $originalRequest["action"],
                        "zendcontroller" => $originalRequest["controller"],
                        "zendmodule" => $originalRequest["module"],
                        "zendparams" => $rq->arrayToUri($originalRequest["params"])
                    ),
                    "email = '" . $email . "'"
                );
            } else {
                $oneshotUser->insert(
                    array(
                        "email" => $email,
                        "name" => $name,
                        "auth_url_id" => $a["auth_url_id"],
                        "zendaction" => $originalRequest["action"],
                        "zendcontroller" => $originalRequest["controller"],
                        "zendmodule" => $originalRequest["module"],
                        "zendparams" => $rq->arrayToUri($originalRequest["params"])
                    )
                );
            }
            // Everything's OK
            $this->forward("sendmail", "oneshot", "auth", $a);
        } else {
            // Please check values...
            $this->forwardNoDispatch("register", "oneshot", "auth", $a);
        }
    }

    /**
     * Send mail for authentication session.
     */
    public function sendmailAction() {
        $ok = true;
        //
        $request = $this->getRequest();
        $email = $request->getParam("email");
        $name = $request->getParam("name");
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
                    "activationlink" => "http://" . ZendHelper::getConfig()->host . "/auth/oneshot/activate/id/" . $authUrlId
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
        // Forward
        if (!$ok) {
            //
            $a = array(
                "email" => $email,
                "name" => $name
            );
            // Please check...
            $this->forward("register", "oneshot", "auth", $a);
        }
    }

    /**
     * Activate oneshot session.
     */
    public function activateAction() {
        $ok = true;
        //
        $rq = new ZendRq($this->getRequest());
        // Get auth url ID from request
        $authUrlId = $rq->id;
        // Persist user in database
        $oneshotUser = new OneshotUser();
        $result = $oneshotUser->findByAuthId($authUrlId);
        if ($result->count()) {
            // Get row
            $row = $result->current();
            $action = $row->zendaction;
            $controller = $row->zendcontroller;
            $module = $row->zendmodule;
            $params = $row->zendparams;
            // Update table with auth information
            $oneshotUser->update(
                array(
                    "auth_url_clicked_on" => date("Y-m-d H:i:s"),
                    "zendaction" => NULL,
                    "zendcontroller" => NULL,
                    "zendmodule" => NULL,
                    "zendparams" => NULL
                ),
                "auth_url_id = '" . $authUrlId . "'"
            );
            // Attempt authentication, saving the result
            $result = ZendHelper::getAuth()->authenticate(new OneshotZendAuthAdapter($row->email));
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
            // Forward
            if ($ok) {
                // Forward to saved request
                if ($action && $controller && $module) {
                    $this->forward($action, $controller, $module, $rq->uriToArray($params));
                }
                //
                $this->view->result = true;
            } else {
                // Forward to auth module
                $this->forwardToOneShotAuthModule(array("email" => $row->email));
            }
        } else {
            // Forward
            $this->forwardToOneShotAuthModule();
        }
    }

    /**
     *
     */
    public function registerAction() {
        // Create instance of image captcha
        $captcha = $this->generateImageCaptcha();
    }

}

?>
