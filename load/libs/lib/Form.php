<?php

/**
 * @name Php Hierarchical Model View Controller
 * @link https://github.com/carlvallory/PMVC Github
 * @version 0.0.5
 * @License https://github.com/carlvallory/PMVC/blob/master/LICENSE Mozilla Public License 2.0
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

/*
 * Validation 
 */

class Form 
{
    private $_currentItem = null;
    private $_postData = array();
    private $_val = array();
    private $_error = array();
    
    public function __construct() {
        $this->_val = $val = new Val();
    }
    
    public function newForm(){
        /* TODO */
    }
    
    public function editForm(){
        /* TODO */
    }
    
    public function delForm(){
        /* TODO */
    }
    
    public function post($field){
        $this->_postData[$field] = $_POST[$field];
        $this->_currentItem = $field;
        
        return $this;
    }
    
    public function fetch($fieldName = false){
        if($fieldName){
            if(isset($this->_postData[$fieldName])){
                return $this->_postData[$fieldName];
            }else{
                return false;
            }
        }else{
            return $this->_postData;
        }
    }
    
    public function val($typeOfValidator, $arg = null){
        // Instantiate Val at the contructor
        if($arg == null){
            $result = $this->_val->{$typeOfValidator}($this->_postData[$this->_currentItem]);
        }else{
            $result = $this->_val->{$typeOfValidator}($this->_postData[$this->_currentItem], $arg);
        }
        if($result){
                $this->_error[$this->_currentItem] = $result;
        }
        return $this;
    }
    
    public function submit(){
        /* TODO */
        
        /*
         * INSERT INTO DATABASE
         */
        
        if(empty($this->_error)){
            return true;
        }else{
            $str = '';
            foreach($this->_error as $key => $value){
                $str .= $key . ' => ' . $value . '\n';
            }
            throw new Exception($str);
        }
    }
}