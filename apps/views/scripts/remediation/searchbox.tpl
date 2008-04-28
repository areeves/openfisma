<div class="barleft">
<div class="barright">
<p><b>Remediation Search</b><span><?php echo date('Y-M-D h:i:s:A');?></span></p>
</div>
</div>
<form name="filters" method="post" action="/zfentry.php/panel/remediation/sub/summary/s/search">
<input type='hidden' name='sort_order' value='{$sort_order}'>
<input type='hidden' name='sort_by'    value='{$sort_by}'>

<!-- Begin Filter Table -->
<table align="center" border="0" cellpadding="3" cellspacing="1" width="95%" class="tipframe">
    <tr>
        <td><b>Finding Source: </b><br>
            <?php echo $this->formSelect('filter_source',$this->criteria['source'],null,$this->source_list);?>
        </td>
        <td>
        <b>ID: </b><i>(You may select multiple IDs by using a comma separated list - x,y,z)</i><br>
        <input type="text" size="70" name="remediation_ids" value="<?php echo $this->remediation_ids;?>">
        </td>
    </tr>
    <tr>
        <td ><b> Mitigation Strategy:</b><br>
            <select name='filter_type'>
            <?php $filter = array('any'  =>'--- Any Type ---',
                                  'NONE' =>'(NONE) Unclassified',
                                  'CAP'  =>'(CAP) Corrective Action Plan',
                                  'AR'   =>'(AR) Accepted Risk',
                                  'FP'   =>'(FP) False Positive');
            foreach($filter as $type=>$value){
                $flag = $type == $this->filter_type?'selected':'';
                echo'<option '.$flag.' value='.$type.'>'.$value.'</option>';
                }
            ?>
            </select> 
        </td>
        <td width="318" valign="top"><b> Finding Status:</b><br>
            <select name='filter_status'>
            <?php $filter = array('any'   =>'--- Any Status ---',
                                  'NEW'       =>'(NEW) Awaiting Mitigation Type and Approval',
                                  'OPEN'      =>'(OPEN) Awaiting Mitigation Approval',
                                  'EN'        =>'(EN) Evidence Needed',
                                  'EO'        =>'(EO) Evidence Overdue',
                                  'EP'        =>'(EP) Evidence Provided',
                                  'EP-SSO'    =>'(EP-SSO) Evidence Provided to SSO',
                                  'ES-SNP'    =>'(EP-S&P) Evidence Provided to S&P',
                                  'ES'        =>'(ES) Evidence Submitted to IV&V',
                                  'CLOSED'    =>'(CLOSED) Officially Closed',
                                  'NOT-CLOSED'=>'(NOT-CLOSED) Not Closed',
                                  'NOUP-30'   =>'(NOUP-30) 30+ Days Since Last Update',
                                  'NOUP-60'   =>'(NOUP-60) 60+ Days Since Last Update',
                                  'NOUP-90'   =>'(NOUP-90) 90+ Days Since Last Update');
            foreach($filter as $status=>$value){
                $flag = $type == $this->filter_status?"selected":"";
                echo'<option '.$flag.' value='.$status.'>'.$value.'</option>';
            }
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td ><b>Asset Owners: </b> <br/>
            <?php echo $this->formSelect('filter_asset_owners',$this->criteria['asset_owner'],null,$this->system_list);?>
        </td>
        <td ><b>Action Owners: </b><br>
            <?php echo $this->formSelect('filter_action_owners',$this->criteria['action_owner'],null,$this->system_list);?>
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
                    <td width="133"><input type="text" name="filter_startdate" size="12" maxlength="10" value="<?php echo $this->filter_startdate;?>">mm/dd/yyyy</td>
                    <td width="33"><span onClick="javascript:show_calendar('filters.filter_startdate');"><img src="/images/picker.gif" width=24 height=22 border=0></span></td>
                    <td width="27">To:</td>
                    <td width="115"><input type="text" name="filter_enddate" size="12" maxlength="10" value="<?php echo $this->filter_enddate;?>"> mm/dd/yyyy</td>
                    <td width="56"><span onClick="javascript:show_calendar('filters.filter_enddate');"><img src="/images/picker.gif" width=24 height=22 border=0></span></td>
                    <td width="47">From:</td>
                    <td width="96"><input type="text" name="filter_startcreatedate" size="12" maxlength="10" value="<?php echo $this->filter_startcreatedate;?>"> mm/dd/yyyy</td>
                    <td width="32"><span onClick="javascript:show_calendar('filters.filter_startcreatedate');"><img src="/images/picker.gif" width=24 height=22 border=0></span></td>
                    <td width="27">To:</td>
                    <td width="115"><input type="text" name="filter_endcreatedate" size="12" maxlength="10" value="<?php echo $this->filter_endcreatedate;?>">mm/dd/yyyy</td>
                    <td width="109"><span onClick="javascript:show_calendar('filters.filter_endcreatedate');"><img src="/images/picker.gif" width=24 height=22 border=0></span></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td align="left"><input type='submit' value='Search' onClick="firstpage();"></td>
    </tr>
</table>
