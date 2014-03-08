<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Parse
 *
 * Serves as a generator for all relevant parse classes.
 *
 */
class Parse {

    public function __construct() {
    }

    public function __construct($array) {
    }

    public function newParseObject($className) {
        include_once 'parse/ParseObject.php';
        return new ParseObject($className);
    }
}

