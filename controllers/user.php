<?php

/**
 * user 
 * Model-View-Controller File
 * 
 * @package MVC
 * @subpackage Controller
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

class User extends Controller {
    
    public function __construct(){
        parent::__construct();
        Auth::handleLogin();
    }

    public function index(){
        $this->view->userList = $this->model->userList();
        $this->view->render('user/index');
    }
    
    public function create(){
        $data = array();
        $data['login'] = $_POST['login'];
        $data['password'] = $_POST['password'];
        $data['nicename'] = $_POST['login'];
        $data['email'] = $_POST['email'];
        $data['role'] = $_POST['role'];
        
        $this->model->create($data);
        
        redirect('user');
    }
    
    public function edit($id){
        $this->view->user = $this->model->userSingleList($id);
        $this->view->render('user/edit'); 
    }
    
    public function editSave($id){
        $data = array();
        $data['id'] = $id;
        $data['login'] = $_POST['login'];
        $data['password'] = $_POST['password'];
        $data['nicename'] = $_POST['login'];
        $data['email'] = $_POST['email'];
        $data['role'] = $_POST['role'];
        
        $this->model->editSave($data);
        
        redirect('user');
    }
    
    public function delete($id)
    {
        $this->model->delete($id);
        redirect('user');
    }
}

?>
