<?php

/**
 * @name Php Hierarchical Model View Controller
 * @link https://github.com/carlvallory/PMVC Github
 * @version 0.0.5
 * @License https://github.com/carlvallory/PMVC/blob/master/LICENSE Mozilla Public License 2.0
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

class View {

    function __construct(){
        //echo 'this is the view<br>';
    }
    public function render($name, $noInclude = false){
        if($noInclude == true){
            require 'views/' . $name . '.php';
        }else{
            require 'views/header.php';
            require 'views/' . $name . '.php';
            require 'views/footer.php';
        }
    }
            
}

?>