<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 * Helper for Zend.
 * @author rbe
 */
class ZendHelper {

    /**
     *
     * @var string
     */
    public static $CONFIGPROFILE = 'configProfile';

    /**
     *
     * @var string
     */
    public static $CONFIG = 'config';

    /**
     * Root directory
     * @access public static
     * @var string
     */
    private static $root = '';

    /**
     * Name of configuration profile to use.
     * @var string
     */
    public static $configProfile = NULL;

    /**
     * Zend configuration.
     * @var Zend_Config
     */
    private static $config = NULL;

    /**
     * Front controller instance
     * @access public static
     * @var object Zend_Controller_Front
     */
    private static $frontController = NULL;

    /**
     * Set root directory of application.
     * @param string $root
     */
    public static function setRoot($root) {
        self::$root = $root;
    }

    /**
     * Get root directory of application.
     */
    public static function getRoot() {
        return self::$root;
    }

    /**
     * Find root directory: go upstairs until directory 'application' is found.
     */
    public static function findRoot() {
        $d = dirname(__FILE__);
    }

    /**
     * Read configuration from ini file.
     * @return Zend_Config
     */
    public static function readConfiguration() {
        $c = self::$root . '/application/config.ini';
        // Read bootstrap section and determine active profile
        $zc = new Zend_Config_Ini($c, "bootstrap");
        self::$configProfile = $zc->profile->active;
        // Read profile
        self::$config = new Zend_Config_Ini($c, self::$configProfile);
        // Put into Zend registry
        self::setRegistryEntry(self::$CONFIGPROFILE, self::$configProfile);
        self::setRegistryEntry(self::$CONFIG, self::$config);
    }

    /**
     * Return value of configuration entry.
     * @param string $name
     * @return mixed
     */
    public static function getConfig() {
        return self::$config;
    }

    /**
     * Prepare front controller-
     * @access public static
     */
    public static function setupFrontController() {
        Zend_Loader::registerAutoload();
        self::$frontController = Zend_Controller_Front::getInstance();
        self::$frontController->throwExceptions(true);
        self::$frontController->setParam('noErrorHandler', false);
        self::$frontController->returnResponse(true);
        self::$frontController->addModuleDirectory(self::$root .  '/application/modules');
    }

    /**
     * Get Zend front controller.
     * @return Zend_Front_Controller
     */
    public static function getFrontController() {
        return self::$frontController;
    }

    /**
     * Setup view.
     * @access public static
     * @return null
     */
    public static function setupView() {
        self::$frontController->registerPlugin(new PhpTalControllerPlugin());
    }

    /**
     * Setup model.
     * @access public static
     * @return null
     */
    public static function setupDatabase() {
        $params = array(
            "host" => self::$config->database->host,
            "username" => self::$config->database->username,
            "password" => self::$config->database->password,
            "dbname" => self::$config->database->name,
            "options" => array()
        );
        $db = Zend_Db::factory(self::$config->database->type, $params);
        Zend_Db_Table::setDefaultAdapter($db);
        // Put into registry
        ZendHelper::setRegistryEntry("db", $db);
    }

    /**
     * Return database connection.
     * @return Zend_Db
     */
    public static function getDatabase() {
        return self::getRegistryEntry("db");
    }

    /**
     * Return content of registry entry.
     * @param string $name
     * @return mixed
     */
    public static function setRegistryEntry($name, $value) {
        return Zend_Registry::set($name, $value);
    }

    /**
     * Return content of registry entry.
     * @param string $name
     * @return mixed
     */
    public static function getRegistryEntry($name) {
        return Zend_Registry::get($name);
    }

    /**
     * Return session namespace.
     * @param string $ns
     * @return Zend_Session_Namespace
     */
    public static function getSessionNs($ns = 'default') {
        return new Zend_Session_Namespace($ns);
    }

    /**
     * Get Zend authentication instance.
     * @return Zend_Auth
     */
    public static function getAuth() {
        return Zend_Auth::getInstance();
    }

    /**
     * Redirect to another URL.
     * @return boolean
     */
    public static function redirectToUrl($url) {
        if (!headers_sent()) {
            // Redirect to auth module, login page
            header("Location: " . $url);
            exit; //return true;
        } else {
            throw new IllegalStateException("Cannot redirect to " . $url . ". Headers were already sent.");
        }
    }

    /**
     * Redirect to authentication URL (see config.ini).
     * @return boolean
     */
    public static function redirectToAuthUrl() {
        if (!headers_sent()) {
            // Redirect to auth module, login page
            header("Location: /" . ZendHelper::getConfig()->auth->module); // TODO Add controller/action
            exit; //return true;
        } else {
            throw new IllegalStateException("PROTECTED AREA: cannot redirect to authentication module (headers were already sent).");
        }
    }

    /**
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public static function saveOriginalRequest(Zend_Controller_Request_Abstract $request) {
        ZendHelper::getSessionNs()->originalModule = $request->getModuleName();
        ZendHelper::getSessionNs()->originalController = $request->getControllerName();
        ZendHelper::getSessionNs()->originalAction = $request->getActionName();
        ZendHelper::getSessionNs()->originalParams = $request->getParams();
    }

    /**
     * Return array for 'original' request destination (module, controller, action).
     * Sets session variables to NULL.
     */
    public static function getOriginalRequest() {
        // Get and reset session variables
        $a['module'] = ZendHelper::getSessionNs()->originalModule;
        ZendHelper::getSessionNs()->originalModule = NULL;
        $a['controller'] = ZendHelper::getSessionNs()->originalController;
        ZendHelper::getSessionNs()->originalController = NULL;
        $a['action'] = ZendHelper::getSessionNs()->originalAction;
        ZendHelper::getSessionNs()->originalAction = NULL;
        $a['params'] = ZendHelper::getSessionNs()->originalParams;
        ZendHelper::getSessionNs()->originalParams = NULL;
        //
        return $a;
    }

    /**
     * Save user in session.
     * @param User $user
     */
    public static function setUser($user) {
        // Start session
        Zend_Session::start();
        // Set user in session
        self::getSessionNs()->user = $user;
        $lifetime = self::getConfig()->zend->session->lifetime;
        if ($lifetime) {
            // Set global session lifetime
            Zend_Session::rememberMe($lifetime);
            // Set session lifetime
            self::getSessionNs()->setExpirationSeconds($lifetime); //$authNamespace = new Zend_Session_Namespace('Zend_Auth');
            // Set session lifetime in PHP
            ini_set("session.cookie_lifetime", $lifetime);
        }
    }

    /**
     * Remove user from session.
     */
    public static function removeUser() {
        // Clear identity
        self::getAuth()->clearIdentity();
        // Remove user objects
        self::getSessionNs()->user = NULL;
        self::getSessionNs()->register_user = NULL;
        // Unset all objects
        // TODO Breaks saveOriginalRequest self::getSessionNs()->unsetAll();
    }

    /**
     * Get user from session.
     * @return User User instance.
     */
    public static function getUser() {
        return self::getSessionNs()->user;
    }

    /**
     * Validate an email address.
     * @param string $email The address.
     * @param boolean $checkMx Check existance of MX host, if supported. Default is true.
     * @return boolean Is email address valid?
     */
    public static function validateEmail($email, $checkMx = true) {
        $ok = true;
        if ($email) {
            $zve = new Zend_Validate_EmailAddress();
            if ($zve->validateMxSupported()) {
                $zve->setValidateMx(true);
            }
            if (!$zve->isValid($email)) {
                $ok = false;
            }
        } else {
            $ok = false;
        }
        //
        return $ok;
    }

}

?>
