<?php

/**
 * @name Php Model View Controller
 * @link https://github.com/carlvallory/PMVC Github
 * @version 0.0.5
 * @License https://github.com/carlvallory/PMVC/blob/master/LICENSE Mozilla Public License 2.0
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

class Chat extends Modules
{
    //TODO
    public function __construct(){
        parent::__construct();
        echo 'this is a module for chat integration<br/>';
        
        if($this->createDB()){
            //Iniciar la conexion
        }
    }
    
    private function createDB(){
        //check if the table for the chat exist
        //if not it creates the table where the chat is saved
        return true;
    }
    
    private function createModel(){
        
    }
    
    private function createView(){
        
    }
    
    private function createController(){
        
    }
    
}
?>