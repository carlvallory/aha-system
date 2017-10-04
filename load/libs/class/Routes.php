<?php

/**
 * @name Php Hierarchical Model View Controller
 * @link https://github.com/carlvallory/PMVC Github
 * @version 0.0.5
 * @License https://github.com/carlvallory/PMVC/blob/master/LICENSE Mozilla Public License 2.0
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

class Routes 
{
    private $_url = null;
    private $_controller = null;
    
    private $_controllerPath = 'controllers/';
    private $_modelPath = 'models/';
    private $_defaultFile = 'index.php';
    private $_errorFile = 'error.php';
    
    function __construct(){

        $this->_getUrl();
        
        if($this->_loadDefaultController() == false){
            return false;
        }
        
        $this->_loadExistingController();
        // calling methods area
        $this->_callControllerMethod();
    }
    
    public function init(){
        
    }
    
    public function setControllerPath($path){
        $this->_controllerPath = trim($path, '/') . '/';
    }
    
    public function setModelPath($path){
        $this->_modelPath = trim($path, '/') . '/';
    }
    
    public function setDefaultFile($path){
        $this->_defaultFile = trim($path, '/');
    }
    
    public function setErrorFile($path){
        $this->_errorFile = trim($path, '/');
    }
    
    private function _getUrl(){
        $url = isset($_GET['url']) ? $_GET['url'] : null;
        $url = rtrim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = explode('/', $url);
        $this->_url = $url;
    }
    
    private function _loadDefaultController(){
        if(empty($this->_url[0])){
            require 'controllers/index.php';
            $this->_controller = new Index();
            $this->_controller->index();
            return false;
        } else {
            return true;
        }
    }
    
    private function _loadExistingController(){
        $file = 'controllers/' . $this->_url[0] . '.php';
        if(file_exists($file)){
            require $file;
            $this->_controller = new $this->_url[0];
            $this->_controller->loadModel($this->_url[0], $this->_modelPath);
        }else{
            $this->_error();
            return false;
        }
    }
    
    private function _callControllerMethod(){
        /*
         * url[0] = Controller
         * url[1] = Method
         * url[2] = Param
         * url[3] = Param2
         * url[4] = Param3
         */
        
        $length = count($this->_url);
        
        if($length > 1) {
            if(!method_exists($this->_controller, $this->_url[1])){
                $this->_error();
            }
        }
        
        switch ($length){
            case 6:
                //Controller->Method(Param1, Param2, Param3, Param4);
                $this->_controller->{$this->_url[1]}($this->_url[2], $this->_url[3], $this->_url[4], $this->_url[5]);
                break;
            case 5:
                //Controller->Method(Param1, Param2, Param3);
                $this->_controller->{$this->_url[1]}($this->_url[2], $this->_url[3], $this->_url[4]);
                break;
            case 4:
                //Controller->Method(Param1, Param2);
                $this->_controller->{$this->_url[1]}($this->_url[2], $this->_url[3]);
                break;
            case 3:
                //Controller->Method(Param1);
                $this->_controller->{$this->_url[1]}($this->_url[2]);
                break;
            case 2:
                //Controller->Method();
                $this->_controller->{$this->_url[1]}();
                break;
            default:
                $this->_controller->index();
                break;
        }
    }
            
    function _error(){
        require $this->_controllerPath . $this->_errorFile;
        $this->_controller = new reError();
        $this->_controller->index();
        return false;
    }

}

class Route
{
    /*
    * Builds a collection of internal URL's to loof for
    * @param type $uri
    */
    private $_uri = array();

    public function add($uri){
        $this->_uri[] = $uri;
    }
}

?>