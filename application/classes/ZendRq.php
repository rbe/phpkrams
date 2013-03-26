<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 * Easy dealing with request data.
 * @author rbe
 */
class ZendRq {

    /**
     * The Zend request object.
     * @var Zend_Controller_Request_Abtract
     */
    private $request;

    /**
     * Construtor.
     * @param Zend_Controller_Request_Abtract $request
     */
    public function ZendRq(Zend_Controller_Request_Abstract $request) {
        $this->request = $request;
    }

    /**
     * Set a requerst parameter.
     * @param string $var
     * @param mixed $val
     */
    public function __set($var, $val) {
        $this->request->setParam($var, $val);
    }

    /**
     * Get value of request parameter.
     * @param string $var
     * @return mixed
     */
    public function __get($var) {
        return $this->request->getParam($var);
    }

    /**
     * Is request parameter set?
     * @param string $var
     * @return boolean
     */
    public function __isset($var) {
        if ($this->request->getParam($var)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Unset a request parameter.
     * @param string $var
     */
    public function __unset($var) {
        $this->request->setParam($var, NULL);
    }

    /**
     * Convert associative array into request URI.
     * @param array $arr
     * @return string
     */
    public function arrayToUri(array $arr) {
        $params = "";
        foreach ($arr as $k => $v) {
            $params .= "&" . $k . "=" . $v;
        }
        return substr($params, 1);
    }

    /**
     * Convert URI string into associative array.
     * @param string $uri
     * @return array
     */
    public function uriToArray($uri) {
        // Check argument
        if (!$uri) {
            return NULL;
        }
        //
        $params = explode("&", $uri);
        $arr[] = array();
        foreach ($params as $p) {
            $kv = explode("=", $p);
            $arr[$kv[0]] = $kv[1];
        }
        return $arr;
    }

}

?>
