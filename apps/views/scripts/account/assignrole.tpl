<div class="barleft">
<div class="barright">
<p><b>Administration: Role and privileges Assignment</b>
</div>
</div>
<form method="post" action="/panel/account/sub/assignrole/do/assign/id/<?php echo $this->user_id;?>" 
    name="assign_role">
<fieldset style="border: 1px solid rgb(68, 99, 122); padding: 3px;"><legend><b><font size="3px"><?php echo $this->user_name;?></font></b></legend>
    <table height="200" style="margin-left:100px">
        <tr>
            <td><b>Available Roles</b></td>
            <td></td>
            <td><b>Assigned Roles</b></td>
        </tr>
        <tr>
            <td width="254"><select multiple size="10" id="available_roles" name="available_roles" style="width:250px">
                <?php foreach($this->available_roles as $row){
                    echo '<option value="'.$row['role_id'].'" title="'.$row['role_name'].'">'.$row['role_name'].'</option>';
                } ?>
                </select><br><br><br>
            <b>Available Privileges</b><br><br>
            <select multiple size="24" id="available_privileges" name="available_privileges" style="width:250px"></select></td>
            <td width="54" valign="top">
                <br><br>
                <input type="button" value="   ->    " id="add_role">
                <br><br>
                <input type="button" value="   <-    " id="remove_role">
                <br><br><br><br><br><br><br><br><br><br><br>
                <input type="button" value="   ->    " id="add_privilege">
                <br><br>
                <input type="button" value="   <-    " id="remove_privilege">
            </td>
            <td width="302">
                <select multiple size="10" id="assign_roles" name="assign_roles[]" style="width:250px" url="/account/searchprivilege/id/<?php echo $this->user_id;?>">
                    <?php foreach($this->assign_roles as $row){
                        echo '<option value="'.$row['role_id'].'" title="'.$row['role_name'].'">'.$row['role_name'].'</option>';
                    } ?>
                </select><br><br>
                <b>Individual Privileges</b><br>(<i>not including assigned roles</i>)<br><br>
                <select multiple size="24" id="assign_privileges" name="assign_privileges[]" style="width:250px">
                    <?php foreach($this->assign_privileges as $row){
                         echo '<option value="'.$row['function_id'].'" title="'.$row['function_name'].'">'.$row['function_name'].'</option>';
                    } ?>
                </select>
            </td>
        </tr>
   </table>
</fieldset>
<table width="500">
<tr align="right">
    <td><input type="submit" value="Save"></td>
    <td><input type="reset" onclick="document.rtable.reset();" value="Reset"></td>
</tr>
</table>
</form>

