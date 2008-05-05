<div class="barleft">
<div class="barright">
<p><b>Reports : POA&amp;M Reports</b><span><?php echo date('Y-M-D h:i:s:A');?></span>
</div>
</div>
<br>
<form name="filter" method="post" action="/zfentry.php/panel/report/sub/searchbox/flag/poam/s/search">
<input type="hidden" name="action" value="filter">
<table width="95%" align="center" border="0" cellpadding="5" cellspacing="1" class="tipframe">
    <tr>
        <td width="6%" height="47"><b>System </b></td>
        <td width="21%">
        <?php echo $this->formSelect('system',$this->criteria['system'],null,$this->system_list);?>
        </td>
        <td width="6%"><b>Source</b></td>
        <td width="18%">
        <?php echo $this->formSelect('source',$this->criteria['source'],null,$this->source_list);?>
        </td>
        <td width="9%"><b>Fiscal Year</b></td>
        <td width="40%">
        <?php echo $this->formSelect('fy',$this->criteria['fy'],null,$this->year_list);?>
        </td>
    </tr>
    <?php if('poam' == $this->flag){ ?>
    <tr>
        <td height="30"><b>Type</b></td>
        <td>
        <?php echo $this->formSelect('type',$this->criteria['type'],null,$this->type_list);?>
        </td>
        <td><b>Status</b></td>
        <td colspan="3">
        <?php echo $this->formSelect('status',$this->criteria['status'],null,$this->status_list);?>
        </td>
    </tr>
    <?php }?>
    <tr>
        <td height="39" colspan="6"><input type="submit" name="search" value="Generate">
        <input type="hidden" name="t" value="{$t}" />
        <input type="hidden" name="sub" value="1" /></td>
    </tr>
</table>

</form>
