<?php

/**
 * Auto-load interfaces and classes.
 * @param string $fqcn
 */
function __autoload($fqcn) {
    if (stristr($fqcn, "Zend_")) {
        // Zend classes
        $token = explode('_', $fqcn);
    } else {
        $token = explode('.', $fqcn);
    }
    // Class
    $fullClassPath = array(
        '../application/classes/',
        '../library/',
        '../library/classes/',
    );
    foreach ($fullClassPath as $fcp) {
        $f =  $fcp . join('/', $token) . '.php';
        if (file_exists($f)) {
            require_once $f;
            return;
        }
    }
    // Interface
    $fullIntfPath = array(
        '../application/intf/',
        '../library/intf/'
    );
    foreach ($fullIntfPath as $fci) {
        $f =  $fcp . join('/', $token) . '.php';
        if (file_exists($f)) {
            require_once $f;
            return;
        }
    }
}

?>
