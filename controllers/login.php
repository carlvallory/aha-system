<?php

/**
 * Login 
 * Model-View-Controller File
 * 
 * @package MVC
 * @subpackage Controller
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

class Login extends Controller {
    
    function __construct(){
        parent::__construct();
    }

    function index(){
        $this->view->render('login/index');
    }
    
    function run(){
        $this->model->run();
    }
}

?>
