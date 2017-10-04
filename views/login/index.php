<?php

/**
 * login
 * Model-View-Controller File
 * 
 * @package MVC
 * @subpackage View
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

?>

<h1>login</h1>

<form action="login/run" method="post">
    
    <label>Login</label><input type="text" name="login" /><br/>
    <label>Password</label><input type="password" name="password" /><br/>
    <label></label><input type="submit" />
</form>

<?php //echo $this->msg; ?>