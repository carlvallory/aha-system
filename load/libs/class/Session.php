<?php

/**
 * @name Php Hierarchical Model View Controller
 * @link https://github.com/carlvallory/PMVC Github
 * @version 0.0.5
 * @License https://github.com/carlvallory/PMVC/blob/master/LICENSE Mozilla Public License 2.0
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

class Session
{
    public static function init(){
        @session_start();
    }
    
    public static function set($key, $value){
        $_SESSION[$key] = $value;
    }
    
    public static function get($key){
        if(isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
    }
    
    public static function destroy(){
        //unset($_SESSION);
        session_destroy();
        redirect('login');
        exit;
    }
}

?>