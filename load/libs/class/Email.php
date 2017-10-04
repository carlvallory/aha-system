<?php

/**
 * @name Php Hierarchical Model View Controller
 * @link https://github.com/carlvallory/PMVC Github
 * @version 0.0.5
 * @License https://github.com/carlvallory/PMVC/blob/master/LICENSE Mozilla Public License 2.0
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

class Email
{
    public $_version = '0.1.0';
    //TODO
    function __construct($to = null, $subject = null, $message = null, $from = null, $param) {
        //echo 'this is the Secure FileSystem class<br>';
        if($to != null && $subject != null && $message != null ){
            $to = null;
            $subject = null; 
            $message = null;

            if($from == null){
                $headers = 'From: ' . OWNERMAIL;
            }else{
                $headers = 'From: ' . $from;
            }
            $parameters = null;
        }
    }
}
?>