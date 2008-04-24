<?php echo $this->msg;?>
<div class="barleft">
<div class="barright">
<p><b>Administration: Users Edit</b>
</div>
</div>
<table border="0" width="95%" align="center">
<tr>
    <td align="left"><font color="blue">*</font> = Required Field</td>
</tr>
</table>
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0" class="tbframe">
<form name="edit" method="post" action="/zfentry.php/user/update/id/<?php echo $this->id;?>">
    <tr>
        <td align="right" class="thc" width="200">Last Name:</td>
        <td class="tdc">&nbsp;<input type="text" name="user_name_last" 
            value="<?php echo $this->user['lastname'];?>" size="90"><font color="blue"> *</font></td>
    </tr>
    <tr>
        <td align="right" class="thc">First Name:</td>
        <td class="tdc">&nbsp;<input type="text" name="user_name_first" 
            value="<?php echo $this->user['firstname'];?>" size="90"><font color="blue"> *</font></td>
    </tr>
    <tr>
        <td align="right" class="thc">Office Phone:</td>
        <td class="tdc">&nbsp;<input type="text" name="user_phone_office"
            value="<?php echo $this->user['officephone'];?>" size="20"><font color="blue"> *</font> </td>
    </tr>
    <tr>
        <td align="right" class="thc">Mobile Phone:</td>
        <td class="tdc">&nbsp;<input type="text" name="user_phone_mobile"
            value="<?php echo $this->user['mobilephone'];?>" size="20"></td>
    </tr>
    <tr>
        <td align="right" class="thc">Email:</td>
        <td class="tdc">&nbsp;<input type="text" name="user_email" 
            value="<?php echo $this->user['email'];?>" size="64"><font color="blue"> *</font></td>
    </tr>
    <tr>
        <td align="right" class="thc">Role:</td>
        <td class="tdc">&nbsp;click here to edit role and privileges for this user</td>
    </tr>
    <tr>
        <td align="right" class="thc">Title:</td>
        <td class="tdc">&nbsp;<input type="text" name="user_title" 
            value="<?php echo $this->user['title'];?>" size="90"></td>
    </tr>
    <tr>
        <td align="right" class="thc">Status:</td>
        <td class="tdc">&nbsp;<select name="user_is_active">
            <option value="1" <?php echo 1 == $this->user['status']?'selected':'';?>>Active</option>
            <option value="0" <?php echo 0 == $this->user['status']?'selected':'';?>>Suspend</option>
        </select></td>
    </tr>
    <tr>
        <td align="right" class="thc">Username:</td>
        <td class="tdc">&nbsp;<input type="text" name="user_name"
            value="<?php echo $this->user['username'];?>" size="90"><font color="blue"> *</font></td>
    </tr>
    <tr>
        <td align="right" class="thc">Password:</td>
        <td class="tdc">&nbsp;<input type="password" name="user_password" value="" size="30"></td>
    </tr>
    <tr>
        <td align="right" class="thc">Confirm Password:</td>
        <td class="tdc">&nbsp;<input type="password" name="confirm_password" value="" size="30">
        <font color="blue">*</font></td>
    </tr>
</table>
<br><br>
<fieldset style="border:1px solid #BEBEBE; padding:3"><legend><b>Systems</b></legend>
<table border="0" width="100%">
<tr>
<?php
    $row = 4;
    $num = 0;
    foreach($this->sys as $system ){
        $num++;
        if($num % $row == 0){
            $flag = "</tr><tr>";
        } else {
            $flag = "";
        }
        if(in_array($system['sid'],$this->sid_arr)){
            $checked = " checked";
        } else {
            $checked ="";
        }
?>
    <td>
       <input type="checkbox" name="system_<?php echo $system['sid'];?>" value="<?php echo $system['sid'];?>" <?php echo $checked;?>>&nbsp;<?php echo $system['sname']; ?>
    </td>
<?php echo $flag;
    } 
?>
</table>
</fieldset>
<table border="0" width="300">
<tr align="center">
    <td><input type="submit" value="Update" title="submit your request"></td>
    <td><span style="cursor: pointer"><input type="reset" value="Reset" onclick="document.edit.reset();"></span></td>
</tr>
</table>
</form>
<br>
