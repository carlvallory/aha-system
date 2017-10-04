<?php

/**
 * Help_Model
 * Model-View-Controller File
 * 
 * @package MVC
 * @subpackage Model
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

class Help_Model extends Model {
    
    function __construct() {
        echo 'Help model<br>';
    }
    
    function blah() {
        return 10 + 10;
    }
}
