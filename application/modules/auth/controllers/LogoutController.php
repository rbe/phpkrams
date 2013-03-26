<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 *
 */
class Auth_LogoutController extends ZendControllerBase {

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
     * Perform logout: clear Zend_Auth identity.
     */
    public function doAction() {
        // Handle case when no one is logged in...
        $this->forwardToAuthModuleIfNoUser();
        // Clear session
        ZendHelper::removeUser();
        //
        ZendHelper::redirectToUrl(ZendHelper::getConfig()->auth->logout->redirecturl);
    }

}

?>
