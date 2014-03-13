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

    public function ParseObject($className) {
        include_once 'parse/ParseObject.php';
        return new ParseObject($className);
    }

    public function ParseUser() {
        include_once 'parse/ParseUser.php';
        return new ParseUser();
    }

    public function ParseQuery($className) {
        include_once 'parse/ParseQuery.php';
        return new ParseQuery($className);
    }
}

