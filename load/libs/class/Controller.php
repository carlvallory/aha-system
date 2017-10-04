<?php

/**
 * @name Php Hierarchical Model View Controller
 * @link https://github.com/carlvallory/PMVC Github
 * @version 0.0.5
 * @License https://github.com/carlvallory/PMVC/blob/master/LICENSE Mozilla Public License 2.0
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

class Controller
{
    function __construct(){
        //echo 'Main controller<br>';
        $this->view = new View();
    
    }
    
    public function loadModel($name, $modelPath){
        
        $path = $modelPath.$name.'_model.php';
        
        if(file_exists($path)){
            require $path;
            $modelName = $name . '_Model';
            $this->model = new $modelName;
        }
    }
}

?>