<br>
<table width="95%" border="0" align="center">
<tr>
<td>
<table border="0" align="left">
    <tr>
        <td>
            <!-- SAVE MODIFICATIONS TO REMEDIATION -->
            <form action="/zfentry.php/panel/remediation/sub/modify/id/<?php echo $this->remediation_id;?>" method="post">
            <input type='hidden' name='action_owner' value=''>
            <input type='hidden' name='action_suggested' value=''>
            <input type='hidden' name='type' value=''>
            <input type='hidden' name='action_planned' value=''>
            <input type='hidden' name='action_resources' value=''>
            <input type='hidden' name='action_est_date' value=''>
            <input type='hidden' name='blscr' value=''>
            <input type='hidden' name='threat_level' value=''>
            <input type='hidden' name='threat_source' value=''>
            <input type='hidden' name='threat_justification' value=''>
            <input type='hidden' name='cmeasure_effectiveness' value=''>
            <input type='hidden' name='cmeasure' value=''>
            <input type='hidden' name='cmeasure_justification' value=''>
            <input type='hidden' name='action_status' value=''>
            <input type='hidden' name='action_approval' value=''>
            <input type='submit' title='Save or Submit' value="Save" style="cursor: pointer;">
            </form>
        </td>
    </tr>
</table>
</td>
</tr>
</table>
<?php 
     echo $this->partial('remediation/finding.tpl',
                          array('finding'=>$this->finding,
                                'remediation_status'  =>$this->remediation_status,
                                'system_list'         =>$this->system_list,
                                'asset_address'       =>$this->asset_address,
                                'product'             =>$this->product,
                                'vulner'              =>$this->vulner,
                                'remediation'         =>$this->remediation));
     echo $this->partial('remediation/mitigation.tpl',
                          array('num_comments_est'    =>$this->num_comments_est,
                                'remediation'         =>$this->remediation,
                                'comments_est'            =>$this->comments_est));
     echo $this->partial('remediation/nist.tpl',
                          array('blscr'               =>$this->blscr,
                                'remediation'         =>$this->remediation,
                                'all_values'          =>$this->all_values));
     echo $this->partial('remediation/risk.tpl',
                          array('threat_level'        =>$this->threat_level,
                                'cmeasure_effectiveness'=>$this->cmeasure_effectiveness,
                                'remediation'           =>$this->remediation,
                                'remediation_id'        =>$this->remediation_id,
                                'remediation_type'      =>$this->remediation_type,
                                'remediation_status'    =>$this->remediation_status,
                                'is_completed'          =>$this->is_completed,
                                'num_comments_sso'      =>$this->num_comments_sso,
                                'comments_sso'          =>$this->comments_sso,
                                'evaluations'           =>$this->evaluations,
                                'num_evidence'          =>$this->num_evidence,
                                'evidences'             =>$this->evidences));
     echo $this->partial('remediation/log.tpl',
                          array('num_logs'              =>$this->num_logs,
                                'logs'                  =>$this->logs));
?>
