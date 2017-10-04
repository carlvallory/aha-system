<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../load/libs/lib/Form.php';
require '../load/libs/lib/Val.php';

if(isset($_REQUEST['run'])){
    try{ 
        $form = new Form();

        $form ->post('name') ->val('minlength', 3) ->post('age') ->val('integer') ->post('gender');

        $a = $form->fetch();
        $b = $form->fetch('age');

        print_r($form);
        
        $form->submit();
    } catch (Exception $e){
        echo $e->getMessage();
    }
}

?>

<form method="post" action="?run">
    Name <input type="text" name="name" />
    Age <input type="text" name="age" />
    Gender <select name="gender">
        <option value="m">Male</option>
        <option value="f">Female</option>
    </select>
    <input type="submit" />
</form>