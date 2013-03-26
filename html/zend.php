<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

// Setup include path
$BASE = dirname(dirname(__FILE__));
set_include_path(
    get_include_path()
    . PATH_SEPARATOR . $BASE . '/application/classes'
    . PATH_SEPARATOR . $BASE . '/library'
    . PATH_SEPARATOR . $BASE . '/library/classes'
);
// Bootstrap
require_once '../library/autoload.php';
Bootstrap::run();

?>
