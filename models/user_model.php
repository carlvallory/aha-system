<?php

/**
 * Login_Model
 * Model-View-Controller File
 * 
 * @package MVC
 * @subpackage Model
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

class User_Model extends Model
{
    public function __construct() {
        parent::__construct();
    }
    //`id``user_login``user_password``user_nicename``user_email``user_modified``user_role``status``hidden``timestamp`
    
    public function userList(){
        return $this->db->select('SELECT id, user_login, user_role FROM users WHERE hidden = 0');
    }
    
    public function userSingleList($id){
        return $this->db->select('SELECT id, user_login, user_role FROM users WHERE id = :id', array(':id' => $id));
    }
    
    //INSERT INTO `users` (`id`, `user_login`, `user_password`, `user_nicename`, `user_email`, `user_modified`, `user_role`, `status`, `hidden`, `timestamp`) VALUES (NULL, 'leuser', 'lepass', 'leuser', 'lemail', CURRENT_TIMESTAMP, 'default', '1', '0', CURRENT_TIMESTAMP);
    public function create($data){
        
        $this->db->insert('users', array(
            'user_login' => $data['login'], 
            'user_password' => Hash::create(HASH_ALGO, $data['password'], HASH_PASSWORD_KEY), 
            'user_nicename' => $data['nicename'],
            'user_email' => $data['email'],
            'user_role' => $data['role']

            
        ));
    }
    
    public function editSave($data){
        $this->db->update('users', array(
            'user_login' => $data['login'], 
            'user_password' => Hash::create(HASH_ALGO, $data['password'], HASH_PASSWORD_KEY), 
            'user_nicename' => $data['nicename'], 
            'user_email' => $data['email'],
            'user_role' => $data['role']
        ), "`id` = {$data['id']}");
    }
    
    public function delete($id)
    {
        $result = $this->db->select('SELECT user_role FROM users WHERE id = :id', array(':id' => $id));
        
        if($result[0]['role'] == 'owner'){
            return false;
        }
        $sth = $this->db->delete('users', "id = '$id'");
    }
}

