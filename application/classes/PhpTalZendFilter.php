<?php

/**
 * Fixes Zend_View inline PHP to use with PHPTAL
 */
class PhpTalZendFilter implements PHPTAL_Filter {

    /**
     *
     * @var array
     */
    protected $search = '';
    /**
     *
     * @var array
     */
    protected $replace = '';

    /**
     * Add ctx-> to this->
     * Used in preg_replace callback function
     *
     * @name _replace
     * @access private
     * @param string $str
     * @return string
     */
    private function _replace($str) {
        $search = array(
            // helpers
            '@\$this->([^\(\s;]+)\s?\((.*)\)@i',
            // variables
            '@\$this->([a-zA-z_0-9^\(]+)@i'
        );
        $replace = array(
             '$ctx->this->\\1(\\2)',
             '$ctx->\\1'
        );
        $str = preg_replace($search, $replace, $str);
        return $str;
    }

    /**
     * String filtering method, returns filtered string
     *
     * @name filter
     * @access public
     * @param string $xhtml
     * @return string
     */
    public function filter($xhtml) {
        // finds PHP code block and performs _replace only inside
        $xhtml = preg_replace('@(<\?(=|php)?\s(.*)\s\?>)@es', '$this->_replace(\'\\1\')', $xhtml);
        return $xhtml;
    }

}

?>
