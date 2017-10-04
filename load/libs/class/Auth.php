<?php

/**
 * @name Php Hierarchical Model View Controller
 * @link https://github.com/carlvallory/PMVC Github
 * @version 0.0.5
 * @License https://github.com/carlvallory/PMVC/blob/master/LICENSE Mozilla Public License 2.0
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

class Auth extends Session
{
    public static function handleLogin(){
        Session::init();
        $logged = Session::get('loggedIn');
    
        if($logged == false){
            Session::destroy();
        }
    }
    
}

?>