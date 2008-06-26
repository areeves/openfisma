<script language="javascript">
    $(function(){
        $(":button[name=select_all]").click(function(){
            $(":checkbox").attr( 'checked','checked' );
        });
        $(":button[name=select_none]").click(function(){
            $(":checkbox").attr( 'checked','' );
        });
    })
</script>

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
<form name="edit" method="post" action="/zfentry.php/panel/account/sub/update/id/<?php echo $this->id;?>">
    <tr>
        <td align="right" class="thc" width="200">Last Name:</td>
        <td class="tdc">&nbsp;<input type="text" name="user[name_last]" 
            value="<?php echo $this->user['lastname'];?>" size="90">
        <font color="blue"> *</font></td>
    </tr>
    <tr>
        <td align="right" class="thc">First Name:</td>
        <td class="tdc">&nbsp;<input type="text" name="user[name_first]" 
            value="<?php echo $this->user['firstname'];?>" size="90">
        <font color="blue"> *</font></td>
    </tr>
    <tr>
        <td align="right" class="thc">Office Phone:</td>
        <td class="tdc">&nbsp;<input type="text" name="user[phone_office]"
            value="<?php echo $this->user['officephone'];?>" size="20"><font color="blue"> *</font> </td>
    </tr>
    <tr>
        <td align="right" class="thc">Mobile Phone:</td>
        <td class="tdc">&nbsp;<input type="text" name="user[phone_mobile]"
            value="<?php echo $this->user['mobilephone'];?>" size="20"></td>
    </tr>
    <tr>
        <td align="right" class="thc">Email:</td>
        <td class="tdc">&nbsp;<input type="text" name="user[email]" 
            value="<?php echo $this->user['email'];?>" size="64"><font color="blue"> *</font></td>
    </tr>
    <tr>
        <td align="right" class="thc">Role:</td>
        <td class="tdc">&nbsp;<?php echo $this->formSelect('user_role',$this->roles[0]['id'],null,$this->role_list);?></td>
    </tr>
    <tr>
        <td align="right" class="thc">Title:</td>
        <td class="tdc">&nbsp;<input type="text" name="user[title]" 
            value="<?php echo $this->user['title'];?>" size="90"></td>
    </tr>
    <tr>
        <td align="right" class="thc">Status:</td>
        <td class="tdc">&nbsp;<select name="user[is_active]">
            <option value="1" <?php echo 1 == $this->user['status']?'selected':'';?>>Active</option>
            <option value="0" <?php echo 0 == $this->user['status']?'selected':'';?>>Suspend</option>
        </select></td>
    </tr>
    <tr>
        <td align="right" class="thc">Account:</td>
        <td class="tdc">&nbsp;<input type="text" name="user[account]"
            value="<?php echo $this->user['username'];?>" size="90"><font color="blue"> *</font></td>
    </tr>
    <tr>
        <td align="right" class="thc">Password:</td>
        <td class="tdc">&nbsp;<input type="password" name="user[password]" value="" size="30"></td>
    </tr>
    <tr>
        <td align="right" class="thc">Confirm Password:</td>
        <td class="tdc">&nbsp;<input type="password" name="confirm_password" value="" size="30">
        <font color="blue">*</font></td>
    </tr>
</table>
<br><br>
<fieldset style="border:1px solid #BEBEBE; padding:3"><legend><b>Systems</b></legend>
<div style="text-align:right"><span style="margin-right:80px;"><input type="button" name="select_all" value="All" />&nbsp;<input type="button" name="select_none" value="None" /></span></div>
<table border="0" width="100%">
<tr>
<?php
    $row = 4;
    $num = 0;
    foreach($this->all_sys as $sid=>$system ){
        $num++;
        if($num % $row == 0){
            $flag = "</tr><tr>";
        } else {
            $flag = "";
        }
        if(in_array($sid, $this->my_systems)){
            $checked = " checked";
        } else {
            $checked ="";
        }
?>
    <td>
       <input type="checkbox" name="system[]" value="<?php echo $sid;?>" <?php echo $checked;?>>&nbsp;<?php echo $system; ?>
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
