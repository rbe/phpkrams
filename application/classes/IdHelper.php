<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 * Generate IDs.
 * @author rbe
 */
class IdHelper {

    /**
     * Generate an ID based on local time: year, month, day, hour, minute,
     * second, millisecond.
     * @return string
     */
    public static function generate() {
        return base64_encode(date("YmdTHisu"));
    }

}

?>
