<?php

/**
 * This header.php file is under the view folder
 * Model-View-Controller File
 * 
 * @package MVC
 * @subpackage View
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

?>
<!doctype html>
<html>
    <head>
        <title>views</title>
        <link rel="stylesheet" type="text/css" href="<?=baseURL;?>public/css/reset.css" />
        <link rel="stylesheet" type="text/css" href="<?=baseURL;?>public/css/default.css" />
        <script type="text/javascript" src="<?=baseURL;?>public/js/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="<?=baseURL;?>public/js/custom.js"></script>
        
        <?php
            if(isset($this->js)){
                foreach ($this->js as $js){
        ?>
        <script type="text/javascript" src="<?=baseURL.'views/'.$js;?>"></script>
        <?php
                }
            }
        ?>
    </head>
    <body>
        <?php
        Session::init();
        ?>
        <div id="header">
            
            <?php if(Session::get('loggedIn') == false):?>
            <a href="<?=baseURL;?>index">Index</a>
            <a href="<?=baseURL;?>help">Help</a>
            <?php endif; ?>
            <?php if(Session::get('loggedIn') == true): ?>
                <a href="<?=baseURL;?>dashboard">Dashboard</a>
                <?php if(Session::get('role') == 'owner'): ?>
                    <a href="<?=baseURL;?>user">Users</a>
                <?php endif; ?>
                <a href="<?=baseURL;?>dashboard/logout">Logout</a>
            <?php else: ?>
                <a href="<?=baseURL;?>login">Login</a>
            <?php endif; ?>
        </div>
        <div id="content">