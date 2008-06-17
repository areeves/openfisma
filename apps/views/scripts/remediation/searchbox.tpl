<?php
    $filter_type = array(0  =>'--- Any Type ---',
                    'NONE' =>'(NONE) Unclassified',
                    'CAP'  =>'(CAP) Corrective Action Plan',
                    'AR'   =>'(AR) Accepted Risk',
                    'FP'   =>'(FP) False Positive');

    $filter_status = array(0    =>'--- Any Status ---',
                    'NEW'       =>'(NEW) Awaiting Mitigation Type and Approval',
                    'OPEN'      =>'(OPEN) Awaiting Mitigation Approval',
                    'EN'        =>'(EN) Evidence Needed',
                    'EO'        =>'(EO) Evidence Overdue',
                    'EP'        =>'(EP) Evidence Provided',
                    'EP-SSO'    =>'(EP-SSO) Evidence Provided to SSO',
                    'EP-SNP'    =>'(EP-S&P) Evidence Provided to S&P',
                    'ES'        =>'(ES) Evidence Submitted to IV&V',
                    'CLOSED'    =>'(CLOSED) Officially Closed',
                    'NOT-CLOSED'=>'(NOT-CLOSED) Not Closed',
                    'NOUP-30'   =>'(NOUP-30) 30+ Days Since Last Update',
                    'NOUP-60'   =>'(NOUP-60) 60+ Days Since Last Update',
                    'NOUP-90'   =>'(NOUP-90) 90+ Days Since Last Update');
    $this->systems[0] ='--Any--';
    asort($this->systems);
    $this->sources[0] ='--Any--';
    asort($this->sources);

?>
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
            <?php echo $this->formSelect('source_id',
                                         nullGet($this->criteria['source_id'],0),
                                         null,
                                         $this->sources);?>
        </td>
        <td>
        <b>ID: </b><i>(You may select multiple IDs by using a comma separated list - x,y,z)</i><br>
        <input type="text" size="70" name="ids" value="<?php echo $this->criteria['ids'];?>">
        </td>
    </tr>
    <tr>
        <td ><b> Mitigation Strategy:</b><br>
        <?php echo $this->formSelect('type',
                                     nullGet($this->criteria['type'],'0'),
                                     null,
                                     $filter_type);?>
        </td>
        <td width="318" valign="top"><b> Finding Status:</b><br>
        <?php echo $this->formSelect('status', 
                                     nullGet($this->criteria['status'],'0'), 
                                     null, 
                                     $filter_status);?>
        </td>
    </tr>
    <tr>
        <td ><b>Asset Owners: </b> <br/>
            <?php echo $this->formSelect('asset_owner',
                                         nullGet($this->criteria['asset_owner'],0),
                                         null,
                                         $this->systems);?>
        </td>
        <td ><b>Action Owners: </b><br>
            <?php echo $this->formSelect('system_id',
                                         nullGet($this->criteria['system_id'],0),
                                         null,
                                         $this->systems);?>
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
                    value="<?php $ts = nullGet($this->criteria['est_date_begin'],'');
                                 if($ts instanceof Zend_Date){
                                     $ts = $ts->toString('Ymd');
                                 }
                             echo $ts; ?>">yyyy-mm-dd</td>
                    <td width="33"><span onclick="javascript:show_calendar('finding.discovereddate');"><img src="/images/picker.gif" border="0" height="22" width="24"></span></td>
                    <td width="27">To:</td>
                    <td width="115"><input type="text" name="est_date_end" size="12" maxlength="10" 
                    value="<?php $ts = nullGet($this->criteria['est_date_end'],'');
                                 if($ts instanceof Zend_Date){
                                     $ts = $ts->toString('Ymd');
                                 }
                             echo $ts; ?>">yyyy-mm-dd</td>
                    <td width="56"><span onclick="javascript:show_calendar('finding.discovereddate');"><img src="/images/picker.gif" border="0" height="22" width="24"></span></td>
                    <td width="47">From:</td>
                    <td width="96"><input type="text" name="created_date_begin" size="12" maxlength="10" 
                    value="<?php $ts = nullGet($this->criteria['created_data_begin'],'');
                                 if($ts instanceof Zend_Date){
                                     $ts = $ts->toString('Ymd');
                                 }
                             echo $ts; ?>">yyyy-mm-dd</td>
                    <td width="32"><span onclick="javascript:show_calendar('finding.discovereddate');"><img src="/images/picker.gif" border="0" height="22" width="24"></span></td>
                    <td width="27">To:</td>
                    <td width="115"><input type="text" name="created_date_end" size="12" maxlength="10" 
                    value="<?php $ts = nullGet($this->criteria['created_data_end'],'');
                                 if($ts instanceof Zend_Date){
                                     $ts = $ts->toString('Ymd');
                                 }
                             echo $ts; ?>">yyyy-mm-dd</td>
                    <td width="109"><span onclick="javascript:show_calendar('finding.discovereddate');"><img src="/images/picker.gif" border="0" height="22" width="24"></span></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td align="left"><input type='submit' value='Search' onClick="firstpage();"></td>
    </tr>
</table>
