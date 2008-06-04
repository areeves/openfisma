<div class="barleft">
<div class="barright">
<p><b>Remediation Search</b><span><?php echo date('Y-M-D h:i:s:A');?></span></p>
</div>
</div>
<form name="filters" method="post" action="/zfentry.php/panel/remediation/sub/searchbox/s/search">
<input type='hidden' name='sort_order' value='{$sort_order}'>
<input type='hidden' name='sort_by'    value='{$sort_by}'>

<!-- Begin Filter Table -->
<table align="center" border="0" cellpadding="3" cellspacing="1" width="95%" class="tipframe">
    <tr>
        <td><b>Finding Source: </b><br>
            <?php echo $this->formSelect('source',$this->criteria['source'],null,$this->source_list);?>
        </td>
        <td>
        <b>ID: </b><i>(You may select multiple IDs by using a comma separated list - x,y,z)</i><br>
        <input type="text" size="70" name="ids" value="<?php echo $this->criteria['ids'];?>">
        </td>
    </tr>
    <tr>
        <td ><b> Mitigation Strategy:</b><br>
        <?php echo $this->formSelect('type',$this->criteria['type'],null,$this->filter_type);?>
        </td>
        <td width="318" valign="top"><b> Finding Status:</b><br>
        <?php echo $this->formSelect('status',$this->criteria['status'],null,$this->filter_status);?>
        </td>
    </tr>
    <tr>
        <td ><b>Asset Owners: </b> <br/>
            <?php echo $this->formSelect('asset_owner',$this->criteria['asset_owner'],null,$this->system_list);?>
        </td>
        <td ><b>Action Owners: </b><br>
            <?php echo $this->formSelect('action_owner',$this->criteria['system_id'],null,$this->system_list);?>
        </td>
    </tr>
    <tr>
        <td colspan='2'>
            <table border="0" cellpadding="3" cellspacing="1" width="98%">
                <tr>
                    <td colspan="5"><b>Estimated Completion Date:</b></td>
                    <td>&nbsp;</td>
                    <td colspan="5"><b>Date Created: </b></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td width="84"> From:</td>
                    <td width="133"><input type="text" name="est_date_begin" size="12" maxlength="10" 
                    value="<?php echoDefault($this->criteria['est_date_begin']);?>">mm/dd/yyyy</td>
                    <td width="33"></td>
                    <td width="27">To:</td>
                    <td width="115"><input type="text" name="est_date_end" size="12" maxlength="10" 
                    value="<?php echoDefault($this->criteria['est_date_end']);?>"> mm/dd/yyyy</td>
                    <td width="56"></td>
                    <td width="47">From:</td>
                    <td width="96"><input type="text" name="created_date_begin" size="12" maxlength="10" 
                    value="<?php echoDefault($this->criteria['created_date_begin']);?>"> mm/dd/yyyy</td>
                    <td width="32"></td>
                    <td width="27">To:</td>
                    <td width="115"><input type="text" name="created_date_end" size="12" maxlength="10" 
                    value="<?php echoDefault($this->criteria['created_date_end']);?>">mm/dd/yyyy</td>
                    <td width="109"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td align="left"><input type='submit' value='Search' onClick="firstpage();"></td>
    </tr>
</table>
