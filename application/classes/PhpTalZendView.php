<?php

/**
 * Extended Zend_View to support PHPTAL
 */
class PhpTalZendView extends Zend_View_Abstract {

    /**
     * PHPTAL object
     * @var object PHPTAL
     */
    protected $_engine = null;

    /**
     * Plug in PHPTAL object into View
     * @name setEngine
     * @access public
     * @param object PHPTAL $engine
     */
    public function setEngine(PHPTAL $engine) {
        $this->_engine = $engine;
        $this->_engine->set('this', $this);
        return $this;
    }

    /**
     * Get PHPTAL object from View
     * @name getEngine
     * @access public
     */
    public function getEngine() {
        return $this->_engine;
    }

    /**
     * Set PHPTAL variables
     *
     * @access public
     * @param string $key variable name
     * @param string $value variable value
     */
    public function __set($key, $value) {
        $this->_engine->set($key, $value);
    }

    /**
     * Get PHPTAL Variable Value
     *
     * @access public
     * @param string $key variable name
     * @return mixed variable value
     */
    public function __get($key) {
        return $this->_engine->$key;
    }

    /**
     * Check if PHPTAL variable is set
     *
     * @access public
     * @param string $key variable name
     */
    public function __isset($key) {
        return isset($this->_engine->$key);
    }

    /**
     * Unset PHPTAL variable
     *
     * @access public
     * @param string $key variable name
     */
    public function __unset($key) {
        if (isset($this->_engine->$key)) {
            unset($this->_engine->$key);
        }
    }

    /**
     * Clone PHPTAL object
     *
     * @access public
     */
    public function __clone() {
        $this->_engine = clone $this->_engine;
    }

    /**
     * Display template
     *
     * @access protected
     */
    protected function _run() {
        $this->_engine->setTemplate(func_get_arg(0));
        try {
            echo $this->_engine->execute();
        } catch (Zend_View_Exception $e) {
            throw new Zend_View_Exception($e);
        }
    }

}