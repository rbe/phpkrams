<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 * A base class for a 'bean' that provides __set(string, mixed) and __get(string).
 * @author rbe
 */
abstract class AbstractBean {

    /**
     * Array for data.
     * @var array
     */
    protected $data = array();
    
    /**
     * Get this bean's data.
     * @return array Data of this bean.
     */
    public function getArray() {
        return $this->data;
    }

    /**
     * Set a variable.
     * @param string $var Name of variable.
     * @param mixed $val Any value.
     * @return mixed Old data of $var
     */
    public function __set($var, $val) {
        $tmp = NULL;
        if (array_key_exists($var, $this->data)) {
            $tmp = $this->data[$var];
        }
        $this->data[$var] = $val;
        return $tmp;
    }

    /**
     * Return value of variable.
     * @param string $var Name of variable.
     * @return mixed Any value.
     */
    public function __get($var) {
        return $this->data[$var];
    }

    /**
     * Is the variable set?
     * @param string $var Name of variable.
     * @return boolean true if variable is set.
     */
    public function __isset($var) {
        if (array_key_exists($var, $this->data)) {
            return true;
        } else {
            return false;
        }
        //return NULL != $this->data[$var] ? true : false;
    }

    /**
     * Unset variable.
     * @param string $var Name of variable.
     */
    public function __unset($var) {
        unset($this->data[$var]);
    }

}

?>
