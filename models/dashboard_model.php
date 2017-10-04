<?php

/**
 * Dashboard_Model
 * Model-View-Controller File
 * 
 * @package MVC
 * @subpackage Model
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

class Dashboard_Model extends Model {
    
    function __construct() {
        parent::__construct();
    }
    
    function xhrInsert(){
        $text = $_POST['text'];
        /*
        $sth = $this->db->prepare('INSERT INTO data(text) VALUES (:text)');
        $sth->execute(array(':text' => $text));
        $data = array('text' => $text, 'id' => $this->db->lastInsertId());
        echo json_encode($data);
        */
        $result = $this->db->insert('data', array('text' => $text));
        $data = array('text' => $text, 'id' => $this->db->lastInsertId());
        echo json_encode($data);
    }
    
    function xhrGetListings(){
        /*
        $sth = $this->db->prepare('SELECT * FROM data');
        $sth->setFetchMode(PDO::FETCH_ASSOC);
        $sth->execute();
        $data = $sth->fetchAll();
        echo json_encode($data);
        */
        $result = $this->db->select("SELECT * FROM data");
        echo json_encode($result);
    }
    
    function xhrDeleteListing(){
        $id = (int) $_POST['id'];
        /*
        $sth = $this->db->prepare('DELETE FROM data WHERE id ="'.$id.'"');
        $sth->execute();
        */
        $this->db->delete('data', "id = '$id'");
    }
}
?>