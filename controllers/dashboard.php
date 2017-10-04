<?php

/**
 * dashboard 
 * Model-View-Controller File
 * 
 * @package MVC
 * @subpackage Controller
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

class Dashboard extends Controller {
    
    function __construct(){
        parent::__construct();
        Auth::handleLogin();
        
        $this->view->js = array('dashboard/js/default.js');
    }

    function index(){
        $this->view->render('dashboard/index');
    }
    
    function logOut(){
        Session::destroy();
        redirect('login');
        exit;
    }
    
    function xhrInsert(){
        $this->model->xhrInsert();
    }
    
    function xhrGetListings(){
        $this->model->xhrGetListings();
    }
    
    function xhrDeleteListing(){
        $this->model->xhrDeleteListing();
    }
    
}

?>
