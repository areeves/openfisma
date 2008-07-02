<?php
    $this->system_list[0] = '--Any--';
?>
<div class="barleft">
<div class="barright">
<p><b>Asset Search</b><span><?php echo date('Y-m-d h:i:s:A');?></span>
</div>
</div>
<form action="/zfentry.php/panel/asset/sub/searchbox/s/search" method="post">
<table width="95%" align="center" cellpadding="3" cellspacing="1" border="0" class="tipframe">
    <tr>
        <td align="left"><b>System:</b></td>
        <td align="left"><b>Vendor:</b></td>
        <td align="left"><b>Product:</b></td>
        <td align="left"><b>Version:</b></td>
        <td align="left"><b>IP Address:</b></td>
        <td align="left"><b>Port</b></td>
        <td align="right">&nbsp;</td>
        <td align="left">&nbsp;</td>
    </tr>
    <tr>
        <td align="left">
            <?php echo $this->formSelect('system_id',nullGet($this->criteria['system_id'],0),null,
                $this->system_list);?>
        </td>
        <td align="left"><input name="vendor" type="text" id="vendor" value="<?php echo $this->criteria['vendor'];?>"></td>
        <td align="left"><input type="text" name="product" value="<?php echo $this->criteria['product'];?>"></td>
        <td align="left"><input name="version" type="text" id="version" value="<?php echo $this->criteria['version'];?>"></td>
        <td align="left"><input type="text" name="ip" value="<?php echo $this->criteria['ip'];?>" maxlength="23"></td>
        <td align="left"><input type="text" name="port" value="<?php echo $this->criteria['port'];?>" size="10"></td>
        <td align="right">&nbsp;</td>
        <td><input name="button" type="submit" id="button" value="Search" style="cursor:hand;"></td>
    </tr>
</table>
</form>
<br>
