<script language="javascript" src="/javascripts/jquery/jquery.validate.js"></script>
<script language="javascript" src="/javascripts/role.validate.js"></script>
<div class="barleft">
<div class="barright">
<p><b>Administration: Roles Edit</b>
</div>
</div>
<table border="0" width="95%" align="center">
<tr>
    <td align="left"><font color="blue">*</font> = Required Field</td>
</tr>
</table>
<form id="roleform" name="edit" method="post" action="/panel/role/sub/update/id/<?php echo $this->id;?>">
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0" class="tbframe">
    <tr>
        <td align="right" class="thc" width="200">Role Name:</td>
        <td class="tdc"><input type="text" name="role[name]" size="50"
            value="<?php echo $this->role['name'];?>">
        <font color="blue"> *</font></td>
    </tr>
    <tr>
        <td align="right" class="thc">Nickname:</td>
        <td class="tdc"><input type="text" name="role[nickname]" size="50"
            value="<?php echo $this->role['nickname'];?>">
        <font color="blue"> *</font></td>
    </tr>
    <tr>
        <td align="right" class="thc" width="200">Description:</td>
        <td class="tdc"><textarea name="role[desc]" cols="80" rows="10"><?php echo $this->role['desc'];?></textarea></td>
    </tr>
   </table>
<br>
<br>
<table width="300" border="0" align="center">
<tr align="center">
    <td><input type="submit" value="Update" title="submit your request"></td>
    <td><span style="cursor: pointer"><input type="reset" value="Reset"></span></td>
</tr>
</table>
</form>
<br>
