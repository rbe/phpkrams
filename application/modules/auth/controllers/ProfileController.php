<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 *
 */
class Auth_ProfileController extends ZendControllerBase {

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
     * Check profile update request.
     */
    public function updateAction() {
        // Check for user
        $this->forwardToAuthModuleIfNoUser();
        //
        $ok = true;
        $em = "";
        $request = $this->getRequest();
        // Get User instance
        $user = ZendHelper::getUser();
        // Check captcha
        if (!$this->validateImageCaptcha($this->getRequest()->getParam("captcha"))) {
            $this->addErrorMessage("Bitte Captcha prüfen!<br/>");
            $ok = false;
        }
        // Check required values
        if (strlen($request->getParam("lastname")) == 0 || strlen($request->getParam("email")) == 0) {
            $this->addErrorMessage("Bitte die Pflichtfelder ausfüllen (mit * gekennzeichnet)!<br/>");
            $ok = false;
        }
        // Check email
        if (!ZendHelper::validateEmail($request->getParam("email"))) {
            $this->addErrorMessage("Bitte die Email-Adresse prüfen!<br/>");
            $ok = false;
        }
        // Check passwords
        if ($request->getParam("p1")) {
            if (/*$request->getParam("p") != $user->getPassword() ||*/ $request->getParam("p1") != $request->getParam("p2")) {
                $this->addErrorMessage("Bitte die Passwörter prüfen!<br/>");
                $ok = false;
            } else {
                $user->setPassword($request->getParam("p1"));
            }
        }
        // Forward
        if ($ok) {
            // Populate User instance with data
            $user->setSalutation($request->getParam("salutation"));
            $user->setTitle($request->getParam("title"));
            $user->setFirstname($request->getParam("firstname"));
            $user->setLastname($request->getParam("lastname"));
            $user->setStreet($request->getParam("street"));
            $user->setZip($request->getParam("zip"));
            $user->setCity($request->getParam("city"));
            $user->setTelephone($request->getParam("telephone"));
            $user->setEmail($request->getParam("email"));
            $user->setHomepage($request->getParam("homepage"));
            // Everything's OK
            $this->forward("sendmail");
        } else {
            // Please check values...
            $this->forwardNoDispatch("show");
        }
    }

    /**
     *
     */
    public function sendmailAction() {
        // Check for user
        $this->forwardToAuthModuleIfNoUser();
        // Get User instance
        $user = ZendHelper::getUser();
        // Persist user in database
        $authUser = new AuthUser();
        $authUser->update(array(
                "username" => $user->getUsername(),
                "password" => $user->getPassword(),
                "salutation" => $user->getSalutation(),
                "title" => $user->getTitle(),
                "firstname" => $user->getFirstname(),
                "lastname" => $user->getLastname(),
                "street" => $user->getStreet(),
                "zip" => $user->getZip(),
                "city" => $user->getCity(),
                "telephone" => $user->getTelephone(),
                "email" => $user->getEmail(),
                "homepage" => $user->getHomepage()
            ),
            "email = '" . $user->getEmail() . "'"
        );
        // Create and send mail
        try {
            $mail = new ZendMailHelper();
            $mail->create(array(
                "subject" => "Flower for Life: Änderung Ihrer Daten",
                "from_address" => "support@flowerforlife.de",
                "from_name" => "Flower for Life - Support",
                "to" => array(
                        array("address" => $user->getEmail(), "name" => trim($user->getFirstname() . " " . $user->getLastname()))
                    ),
                "body" => "<html><body><p>Ihre neuen Daten:</p>"
                    . "<table>"
                    . "<tr>"
                    . "<td>Anrede</td>"
                    . "<td>" . $user->getSalutation() . "</td>"
                    . "</tr>"
                    . "<tr>"
                    . "<td>Titel</td>"
                    . "<td>" . $user->getTitle() . "</td>"
                    . "</tr>"
                    . "<tr>"
                    . "<td>Vorname</td>"
                    . "<td>" . $user->getFirstname() . "</td>"
                    . "</tr>"
                    . "<tr>"
                    . "<td>Nachname</td>"
                    . "<td>" . $user->getLastname() . "</td>"
                    . "</tr>"
                    . "<tr>"
                    . "<td>Stra&szlig;e</td>"
                    . "<td>" . $user->getStreet() . "</td>"
                    . "</tr>"
                    . "<tr>"
                    . "<td>PLZ Ort</td>"
                    . "<td>" . $user->getZip() . " " . $user->getCity() . "</td>"
                    . "</tr>"
                    . "<tr>"
                    . "<td>Telefon</td>"
                    . "<td>" . $user->getTelephone() . "</td>"
                    . "</tr>"
                    . "<tr>"
                    . "<td>Email</td>"
                    . "<td>" . $user->getEmail() . "</td>"
                    . "</tr>"
                    . "<tr>"
                    . "<td>Passwort</td>"
                    . "<td>" . $user->getPassword() . "</td>"
                    . "</tr>"
                    . "</table>"
                    . "</body></html>"
                ))
            //        ->inlineAttachment("_IMG_", array(
            //                "path" => "",
            //                "type" => ""
            //            ))
            ->send();
            // Success
            $this->forwardNoDispatch("show", "profile", "auth", array(
                    "statusMessage" => "Ihre Daten wurden geändert und Ihnen per Email zugeschickt!"
                )
            );
        } catch (Exception $e) {
            // Set errorMessage in request
            $this->addErrorMessage($e->getMessage());
            // Please check values...
            $this->forward("show");
        }
    }

    /**
     * Show user profile.
     */
    public function showAction() {
        // Check for user
        $this->forwardToAuthModuleIfNoUser();
        // Get user from session
        $user = ZendHelper::getUser();
        // Generate captcha
        $captcha = $this->generateImageCaptcha();
    }

}

?>
