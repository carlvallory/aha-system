<?php

/**
 * user
 * Model-View-Controller File
 * 
 * @package MVC
 * @subpackage View
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

?>

<h1>User: Edit</h1>

<form method="post" action="<?php echo baseURL;?>user/editSave/<?=$this->user[0]['id'];?>">
    <label>Login</label><input type="text" name="login" value="<?php echo $this->user[0]['user_login']; ?>" /><br />
    <label>Password</label><input type="text" name="password" /><br />
    <label>Role</label>
        <select name="role">
            <option value="default" <?php if($this->user[0]['user_role'] == 'default') echo 'selected'; ?>>Default</option>
            <option value="admin" <?php if($this->user[0]['user_role'] == 'admin') echo 'selected'; ?>>Admin</option>
            <option value="owner" <?php if($this->user[0]['user_role'] == 'owner') echo 'selected'; ?>>Owner</option>
        </select><br />
    <label>&nbsp;</label><input type="submit" />
</form>