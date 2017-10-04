<?php

/**
 * Help 
 * Model-View-Controller File
 * 
 * @package MVC
 * @subpackage Controller
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

class Help extends Controller {

    function __construct() {
        parent::__construct();    
    }
    
    function index(){
        $this->view->render('help/index');
    }

    public function other($arg = false){
        require 'models/help_model.php';
        $model = new Help_Model();
        $this->view->blah = $model->blah();
    }

}

?>