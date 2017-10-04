<?php

/**
 * @name Php Hierarchical Model View Controller
 * @link https://github.com/carlvallory/PMVC Github
 * @version 0.0.5
 * @License https://github.com/carlvallory/PMVC/blob/master/LICENSE Mozilla Public License 2.0
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

/* TODO */

function check_version($version, $required_version){
    if (version_compare($version, $required_version, '>=')) {
        return true;
    } else {
        return false;
    }
}

function redirect($page){
    header('Location: ' . baseURL . $page);
}

function datetimetostring($timestamp){
    return date('mdYhia', $timestamp);
}

?>