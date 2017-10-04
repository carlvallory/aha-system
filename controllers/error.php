<?php

/**
 * reError 
 * Model-View-Controller File
 * 
 * @package MVC
 * @subpackage Controller
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

class reError extends Controller {

	function __construct(){
		parent::__construct();
	}

        function index(){
            $this->view->msg = 'This page doesnt exist';
            $this->view->render('error/index');
        }

}