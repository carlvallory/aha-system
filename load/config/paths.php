<?php

/**
 * @name Php Hierarchical Model View Controller
 * @link https://github.com/carlvallory/PMVC Github
 * @version 0.0.5
 * @License https://github.com/carlvallory/PMVC/blob/master/LICENSE Mozilla Public License 2.0
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

define('URL', 'http://' . $_SERVER['SERVER_NAME'] . '/');
define('baseURL', URL . 'phmvc/'); //Mod. .htaccess

define('pathToLib', 'load/libs/');
define('pathToModule', 'load/modules/');

/* ROUTES */
define('controllerPath', 'controllers/');
define('modelPath', 'models/');
define('defaultFile', 'index.php');
define('errorFile', 'error.php');

?>