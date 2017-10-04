<?php

/**
 * @name Php Model View Controller
 * @link https://github.com/carlvallory/PMVC Github
 * @version 0.0.5
 * @License https://github.com/carlvallory/PMVC/blob/master/LICENSE Mozilla Public License 2.0
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

spl_autoload_register(function ($class) {
    if(!class_exists($class)){
        if (file_exists(pathToModule.$class . '.class.php')){
            include pathToModule . $class . '.class.php';
        }
    }
});