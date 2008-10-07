<script language="javascript" src="/javascripts/jquery/jquery.validate.js"></script>
<script language="javascript" src="/javascripts/system.validate.js"></script>
<?php
    $primary_array = array('0'=>'FSA');
    $states_array = array('HIGH'=>'High','MODERATE'=>'Moderate','LOW'=>'Low');
    $type_array   = array('GENERAL SUPPORT SYSTEM'=>'GENERAL SUPPORT SYSTEM',
                          'MINOR APPLICATION'=>'MINOR APPLICATION',
                          'MAJOR APPLICATION'=>'MAJOR APPLICATION');
?>
<div class="barleft">
<div class="barright">
<p><b>System Information</b>
</div>
</div>
<table border="0" width="95%" align="center">
<tr>
    <td align="left"><font color="blue">*</font> = Required Field</td>
</tr>
</table>
<form id="systemform" name="edit" method="post" action="/panel/system/sub/update/id/<?php echo $this->id;?>">
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0" class="tbframe">

    <tr>
        <td align="right" class="thc" width="200">System Name:</td>
        <td class="tdc">&nbsp;<input type="text" name="system[name]" size="50"
            value="<?php echo $this->system['name'];?>">
        <font color="blue"> *</font></td>
    </tr>
    <tr>
        <td align="right" class="thc">Acronym:</td>
        <td class="tdc">&nbsp;<input type="text" name="system[nickname]" size="8"
            value="<?php echo $this->system['nickname'];?>"><font color="blue"> *</font></td>
    </tr>
    <tr>
        <td align="right" class="thc">Organization:</td>
        <td class="tdc">&nbsp;<?php echo $this->formSelect('system[primary_office]',$this->system['primary_office'],null,$primary_array);?><font color="blue">*</font> </td>
    </tr>
    <tr>
        <td align="right" class="thc">Confidentiality:</td>
        <td class="tdc">&nbsp;<?php echo $this->formSelect('system[confidentiality]',$this->system['confidentiality'],null,$states_array);?><font color="blue">*</font></td>            
    </tr>
    <tr>
        <td align="right" class="thc">Integrity:</td>
        <td class="tdc">&nbsp;<?php echo $this->formSelect('system[integrity]',$this->system['integrity'],null,$states_array);?><font color="blue">*</font></td>
    </tr>
    <tr>
        <td align="right" class="thc">Availability:</td>
        <td class="tdc">&nbsp;<?php echo $this->formSelect('system[availability]',$this->system['availability'],null,$states_array);?><font color="blue">*</font></td>
    </tr>
    <tr>
        <td align="right" class="thc">Type:</td>
        <td class="tdc">&nbsp;<?php echo $this->formSelect('system[type]',$this->system['type'],null,$type_array);?><font color="blue">*</font></td>
    </tr>
    <tr>
        <td align="right" class="thc" width="200">Description:</td>
        <td class="tdc">&nbsp;<textarea name="system[desc]" size="30" cols="80" rows="5"><?php echo $this->system['desc'];?></textarea></td>
    </tr>
    <tr>
        <td align="right" class="thc" width="200">Criticality Justification:</td>
        <td class="tdc">&nbsp;<textarea name="system[criticality_justification]" cols="80" rows="5"><?php echo $this->system['criticality_justification'];?></textarea></td>
    </tr>
    <tr>
        <td align="right" class="thc" width="200">Sensitivity Justification:</td>
        <td class="tdc">&nbsp;<textarea name="system[sensitivity_justification]" cols="80" rows="5"><?php echo $this->system['sensitivity_justification'];?></textarea></td>
    </tr>
</table>
<br>
<?php if(!empty($this->sg_list)){
    $i = 0;
    $num = 5;
?>
<fieldset><legend><b>System Groups</b></legend>
<input name="checkhead" value="sysgroup_" type="hidden">
<input name="checktip" value="System Group" type="hidden">
<label for="sysgroup[]" class="error">Please select at least one system group.</label>
<table border="0" width="100%">
<tr>
<?php foreach($this->sg_list as $row){
    $i++;
    $flag = $i%$num == 0?'</tr><tr>':'';
    if(in_array($row,$this->user_sysgroup_list)){
        $checked = 'checked';
    }else{
        $checked = '';
    }
?>
    <td align="right"><input name="sysgroup[]" value="<?php echo $row['id'];?>" type="checkbox" <?php echo $checked;?>></td>
    <td><span title="<?php echo $row['nickname'];?>" style="cursor: pointer;"><?php echo $row['name'];?></span></td>
<?php echo $flag; } ?>

</table></fieldset>
<?php } ?>
<br>
<table border="0" width="300">
<tr align="center">
    <td><input type="submit" value="Update" title="submit your request"></td>
    <td><span style="cursor: pointer"><input type="reset" value="Reset"></span></td>
</tr>
</table>
</form>
<br>
