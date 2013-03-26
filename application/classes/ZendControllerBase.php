<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 * Controller base class.
 * @author rbe
 */
abstract class ZendControllerBase extends Zend_Controller_Action {

    /**
     * The status message.
     * @var string
     */
    protected $statusMessage = NULL;

    /**
     * The error message.
     * @var string
     */
    protected $errorMessage = NULL;

    /**
     *
     */
    public function init() {
        $this->initView();
    }

    /**
     *
     */
    public function preDispatch() {
        parent::preDispatch();
        // Set script path for PHPTAL templates
        // This is done in PhpTalControllerPlugin but after using _forward
        // in a Controller the path gets lost...?!
        $this->view->setScriptPath(ZendHelper::getRoot()
            . "/application/modules/"
            . $this->getRequest()->getModuleName()
            . ZendHelper::getConfig()->phptal->template->dir);
        // Check error message
        $this->checkMessages();
        // Add all request parameters to view
        foreach ($this->getRequest()->getParams() as $k => $v) {
            $this->view->$k = $v;
        }
        // Check authentication
        if ($this->checkAuth() && $this->checkOneShotAuth()) {
            // Copy user object from session into template variable.
            $user = ZendHelper::getUser();
            $this->view->user = $user != NULL ? $user : NULL;
        }
    }

    /**
     *
     */
    public function postDispatch() {
        parent::postDispatch();
    }

    /**
     * Add a status message.
     * @param string $text
     */
    public function addStatusMessage($text) {
        if (empty($this->statusMessage)) {
            $this->statusMessage = $text;
        } else {
            $this->statusMessage .= $text;
        }
    }

    /**
     * Retrieve status message.
     * @return string
     */
    public function getStatusMessage() {
        return $this->statusMessage;
    }

    /**
     * Clear status message.
     */
    public function clearStatusMessage() {
        $this->statusMessage = NULL;
    }

    /**
     * Add an error message.
     * @param string $text
     */
    public function addErrorMessage($text) {
        if (empty($this->errorMessage)) {
            $this->errorMessage = $text;
        } else {
            $this->errorMessage .= $text;
        }
    }

    /**
     * Retrieve error message.
     * @return string
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    }

    /**
     * Clear error message (in request).
     */
    public function clearErrorMessage() {
        $this->errorMessage = NULL;
    }

    /**
     * Check for error message in request.
     */
    public function checkMessages() {
        // Check status message in request
        $rem = $this->getRequest()->getParam("statusMessage");
        // Variable status message must at least be NULL
        if (!empty($rem)) {
            $this->statusMessage = $rem;
        } else {
            $this->statusMessage = NULL;
        }
        // Set status message in view
        $this->view->statusMessage = $this->statusMessage;
        // Check error message in request
        $rem = $this->getRequest()->getParam("errorMessage");
        // Variable error message must at least be NULL
        if (!empty($rem)) {
            $this->errorMessage = $rem;
        } else {
            $this->errorMessage = NULL;
        }
        // Set error message in view
        $this->view->errorMessage = $this->errorMessage;
    }

    /**
     * Set dispatched flag in request to false and forward.
     * @param string $action
     * @param string $controller
     * @param string $module
     */
    public function forward($action, $controller = NULL, $module = NULL, $param = NULL) {
        if ($this->statusMessage) {
            $param["statusMessage"] = $this->statusMessage;
        }
        if ($this->errorMessage) {
            $param["errorMessage"] = $this->errorMessage;
        }
        $this->_forward($action, $controller, $module, $param);
    }

    /**
     * Set dispatched flag in request to false and forward.
     * @param string $action
     * @param string $controller
     * @param string $module
     */
    public function forwardNoDispatch($action, $controller = NULL, $module = NULL, $param = NULL) {
        $this->getRequest()->setDispatched(false);
        $this->forward($action, $controller, $module, $param);
    }

    /**
     * Forward to original request (as got from ZendHelper::getOriginalRequest()).
     * @param array $originalRequest
     * @param array $param
     */
    public static function forwardToOriginalRequest(array $originalRequest, array $param = NULL) {
        $this->forward(
            $originalRequest["action"],
            $originalRequest["controller"],
            $originalRequest["module"],
            $param
        );
    }

    /**
     * Forward to auth module, controller and action index.
     */
    public function forwardToAuthModule(array $param = NULL) {
        $this->forward(
            ZendHelper::getConfig()->auth->action,
            ZendHelper::getConfig()->auth->controller,
            ZendHelper::getConfig()->auth->module,
            $param
        );
    }

    /**
     * Forward to auth module when no user exists in session.
     * @return void
     */
    public function forwardToAuthModuleIfNoUser() {
        // If no user is logged in redirect to auth module
        if (!ZendHelper::getUser()) {
            $this->forwardToAuthModule();
        }
    }

    /**
     * Check authentication.
     * @return boolean
     */
    private function checkAuth() {
        // Is an identity present?
        if (!ZendHelper::getAuth()->hasIdentity()) {
            // Check if module/controller/action is protected
            $modules = explode(':', ZendHelper::getConfig()->auth->protected->modules);
            // Check for protected modules
            foreach ($modules as $m) {
                if($m == $this->getRequest()->getModuleName()) {
                    // Save original URI for later usage
                    ZendHelper::saveOriginalRequest($this->getRequest());
                    // For our safety: remove user from session
                    ZendHelper::removeUser();
                    // Forward to auth module
                    $this->forwardToAuthModule();
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Forward to oneshot auth module, controller and action index.
     */
    public function forwardToOneShotAuthModule(array $param = NULL) {
        $this->forward(
            ZendHelper::getConfig()->auth->oneshot->action,
            ZendHelper::getConfig()->auth->oneshot->controller,
            ZendHelper::getConfig()->auth->oneshot->module,
            $param
        );
    }

    /**
     * Check for oneshot authentication.
     * @return boolean
     */
    private function checkOneShotAuth() {
        // Is an identity present?
        if (!ZendHelper::getAuth()->hasIdentity()) {
            // Check if module/controller/action is protected
            $modules = explode(':', ZendHelper::getConfig()->auth->oneshot->protected->modules);
            // Check for protected modules
            foreach ($modules as $m) {
                if($m == $this->getRequest()->getModuleName()) {
                    // Save original URI for later usage
                    ZendHelper::saveOriginalRequest($this->getRequest());
                    // For our safety: remove user from session
                    ZendHelper::removeUser();
                    // Forward to auth module
                    $this->forwardToOneShotAuthModule();
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Create an instance of an image captcha.
     * @return Zend_Captcha_Image
     */
    public function generateImageCaptcha(array $prop = NULL) {
        if (!$prop) {
            $prop = array(
                "name" => "the_captcha",
                "timeout" => ZendHelper::getConfig()->captcha->timeout,
                "font" => ZendHelper::getConfig()->captcha->image->font, //ZendHelper::getRoot() . "/application/fonts/verdana.ttf",
                "imgDir" => ZendHelper::getConfig()->captcha->tmp->dir, //"./_files/_captcha",
                "imgUrl" => ZendHelper::getConfig()->captcha->url, //"/_files/_captcha",
                "width" => ZendHelper::getConfig()->captcha->image->width,
                "dotNoiseLevel" => ZendHelper::getConfig()->captcha->image->dotnoiselevel,
                "wordLen" => ZendHelper::getConfig()->captcha->image->word->len,
            );
        }
        // Generate captcha
        $captcha = new Zend_Captcha_Image($prop);
        // Generate captcha id and save (for use in input hidden in form)
        $this->view->cptid = $captcha->generate();
        // Render image and save URI
        $this->view->cptimg = $captcha->render(new Zend_View());
        //
        return $captcha;
    }

    /**
     * Validate captcha.
     * @param mixed $captcha Captcha from request.
     * @return boolean Is captcha valid?
     */
    public function validateImageCaptcha($captcha) {
        $imgcpt = new Zend_Captcha_Image();
        if ($imgcpt->isValid($captcha)) {
            return true;
        } else {
            return false;
        }
    }

}
?>
