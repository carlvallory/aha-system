<?php

/**
 * dashboard
 * Model-View-Controller File
 * 
 * @package MVC
 * @subpackage View
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

?>

Dashboard... Logged in only..

<br/>

<form id="randomInsert" action="<?=baseURL;?>dashboard/xhrInsert" method="post">
    <input type="text" name="text" />
    <input type="submit" />
</form>

<br/>

<div id="listInserts">
    
</div>