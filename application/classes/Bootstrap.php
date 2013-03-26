<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 * Bootstrap.
 * @author rbe
 */
class Bootstrap {

    /**
     * Run app
     * @access public static
     * @return null
     */
    public static function run() {
        // TODO Implement and use ZendHelper::findRoot
        ZendHelper::setRoot(dirname(dirname(dirname(__FILE__))));
        // Load configuration
        ZendHelper::readConfiguration();
        // Setup PHP environment
        self::setupPhpEnvironment();
        // Setup Zend
        self::setupZend();
        // Dispatch
        $response = ZendHelper::getFrontController()->dispatch();
        self::sendResponse($response);
    }

    /**
     * Setup environment.
     * @param
     * @return null
     */
    private static function setupPhpEnvironment() {
        $de = ZendHelper::getConfig()->php->display->errors;
        if ($de) {
            error_reporting(E_ALL | E_STRICT);
            ini_set('display_errors', "true");
        } else {
            error_reporting(E_ERROR);
            ini_set('display_errors', "false");
        }
        date_default_timezone_set(ZendHelper::getConfig()->time->zone);
    }

    /**
     * Prepare front controller and view.
     * @return null
     */
    private static function setupZend() {
        ZendHelper::setupDatabase();
        ZendHelper::setupFrontController();
        ZendHelper::setupView();
    }

    /**
     * Send response to the browser
     * @param  Zend_Controller_Response_Http $response
     * @return null
     */
    private static function sendResponse(Zend_Controller_Response_Http $response) {
        $response->setHeader('Content-Type', 'text/html; charset=UTF-8');
        //$response->setHeader('Accept-encoding', 'gzip,deflate');
        $response->sendResponse();
    }

}
