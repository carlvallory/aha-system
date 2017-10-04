<?php

/**
 * @name Php Hierarchical Model View Controller
 * @link https://github.com/carlvallory/PMVC Github
 * @version 0.0.5
 * @License https://github.com/carlvallory/PMVC/blob/master/LICENSE Mozilla Public License 2.0
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

/*
 * Validation 
 */

class Val 
{
    public function __construct() {
        //Empty
    }
    
    public function minlength($data, $arg){
        if(strlen($data) < $arg){
            return "Your string should be $arg or longer.";
        }
    }
    
    public function maxlength($data, $arg){
        if(strlen($data) > $arg){
            return "Your string should only be $arg long.";
        }
    }
    
    public function integer($data, $arg = null){
        if(ctype_digit($data) == false){
            return "Your string must be a digit.";
        }
    }
    
    public function __call($name, $arguments) {
        throw new Exception("$name does not exist inside of: " . __CLASS__);
    }
}
?>