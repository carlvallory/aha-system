<?php

/**
 * Login_Model
 * Model-View-Controller File
 * 
 * @package MVC
 * @subpackage Model
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

class Login_Model extends Model
{
    public function __construct() {
        parent::__construct();
    }
    
    public function run(){
        $login = $_POST['login'];
        $password = Hash::create(HASH_ALGO, $_POST['password'], HASH_PASSWORD_KEY);
        $sth = $this->db->prepare("SELECT id, user_role FROM users WHERE user_login = :login AND user_password = :password");
        $sth->execute(array(':login' => $login, ':password' => $password));
        $data = $sth->fetch();
        //$data = $sth->fetchALL();
        $count = $sth->rowCount();
        if($count>0){
            //login
            Session::init();
            Session::set('role', $data['user_role']);
            Session::set('loggedIn', true);
            Session::set('userid', $data['id']);
            redirect('dashboard');
        }else{
            // show an error!
            redirect('login');
        }
    }
}

