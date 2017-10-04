<?php

/**
 * Index 
 * Model-View-Controller File
 * 
 * @package MVC
 * @subpackage Controller
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

class Index extends Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index(){
        $this->view->render('index/index');
    }
    function details(){
        $this->view->render('index/index');
    }
    
}

?>