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
<p><b>Administration: Users Create</b>
</div>
</div>
<table border="0" width="95%" align="center">
<tr>
    <td align="left"><font color="blue">*</font> = Required Field</td>
</tr>
</table>
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0" class="tbframe">
<form name="edit" method="post" action="/zfentry.php/panel/account/sub/save">
    <tr>
        <td align="right" class="thc" width="200">Last Name:</td>
        <td class="tdc">&nbsp;<input type="text" name="user_name_last" size="90"><font color="blue"> *</font></td>
    </tr>
    <tr>
        <td align="right" class="thc">First Name:</td>
        <td class="tdc">&nbsp;<input type="text" name="user_name_first" size="90"><font color="blue"> *</font></td>
    </tr>
    <tr>
        <td align="right" class="thc">Office Phone:</td>
        <td class="tdc">&nbsp;<input type="text" name="user_phone_office" size="20"><font color="blue"> *</font> </td>
    </tr>
    <tr>
        <td align="right" class="thc">Mobile Phone:</td>
        <td class="tdc">&nbsp;<input type="text" name="user_phone_mobile" size="20"></td>
    </tr>
    <tr>
        <td align="right" class="thc">Email:</td>
        <td class="tdc">&nbsp;<input type="text" name="user_email" size="64"><font color="blue"> *</font></td>
    </tr>
    <tr>
        <td align="right" class="thc">Role:</td>
        <td class="tdc">&nbsp;<?php echo $this->formSelect('user_role_id',null,null,$this->roles);?><font color="blue">*</font></td>
    </tr>
    <tr>
        <td align="right" class="thc">Title:</td>
        <td class="tdc">&nbsp;<input type="text" name="user_title" size="90"></td>
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
        <td class="tdc">&nbsp;<input type="text" name="user_account" size="90"><font color="blue"> *</font></td>
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
<div style="text-align:right"><span style="margin-right:80px;"><input type="button" name="select_all" value="All" />&nbsp;<input type="button" name="select_none" value="None" /></span></div>
<table border="0" width="100%">
<tr>
<?php
    $row = 4;
    $num = 0;
    foreach($this->systems as $id=>$system ){
        $num++;
        if($num % $row == 0){
            $flag = "</tr><tr>";
        } else {
            $flag = "";
        }
?>
    <td>
       <input type="checkbox" name="system_<?php echo $id;?>" value="<?php echo $id;?>">&nbsp;<?php echo $system['name']; ?>
    </td>
<?php echo $flag;
    } 
?>
</table>
</fieldset>
<table border="0" width="300">
<tr align="center">
    <td><input type="submit" value="Create" title="submit your request"></td>
    <td><span style="cursor: pointer"><input type="reset" value="Reset"></span></td>
</tr>
</table>
</form>
<br>
