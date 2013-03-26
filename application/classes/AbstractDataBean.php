<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 * A base class for a 'data bean'. All values will be trimmed.
 * @author rbe
 */
abstract class AbstractDataBean extends AbstractBean {

    /**
     * Set a variable and trim value first.
     * @param string $var Name of variable.
     * @param mixed $val Any value.
     * @return mixed Old data of $var
     */
    public function __set($var, $val) {
        $tmp = NULL;
        if (array_key_exists($var, $this->data)) {
            $tmp = $this->data[$var];
        }
        $this->data[$var] = trim($val);
        return $tmp;
    }

}

?>
