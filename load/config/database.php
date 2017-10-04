<?php

/** 
 * @name Php Hierarchical Model View Controller
 * @link https://github.com/carlvallory/PMVC Github
 * @version 0.0.5
 * @License https://github.com/carlvallory/PMVC/blob/master/LICENSE Mozilla Public License 2.0
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/
    if(LOCAL) {
        define('DB_TYPE','mysql'); //DB_CONNECTION
        define('DB_HOST','localhost');
        define('DB_NAME','phmvc');
        define('DB_USER','root');
        define('DB_PASS','');
        define('DB_PREFIX', '');
        //define('DB_PGSQL_SCHEMA','public');
    }else{
        define('DB_TYPE','mysql'); //DB_CONNECTION
        define('DB_HOST','www.carlvallory.biz');
        define('DB_NAME','prmvc');
        define('DB_USER','carlvallory');
        define('DB_PASS','mypass');
        define('DB_PREFIX', '');
    }
    
    
?>
