<br>
<table width="95%" border="0" align="center">
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
            <input type='hidden' name='blscr_id' value=''>
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
<?php 
     echo $this->partial('remediation/finding.tpl',
                          array('poam'=>&$this->poam, 'system_list' =>&$this->system_list));
     echo $this->partial('remediation/mitigation.tpl',  array('poam'    =>&$this->poam));
     echo $this->partial('remediation/nist.tpl', array('poam'=>&$this->poam));
?>
     <!-- Heading Block -->
     <div class="barleft">
     <div class="barright">
     <p><b>Supporting Evidence</b>(<?php echo count($this->ev_evals);?> total)<span></span></p>
     </div>
     </div>

     <?php 
          echo $this->partialLoop('remediation/evidence.tpl', $this->ev_evals );
     ?>

     <?php if($this->poam['status'] == 'EN' && isAllow('remediation','update_evidence') ){ ?>
     <button id="up_evidence" onclick ="upload_evidence();">Upload Evidence</button>
     <?php } ?>

     <!-- Heading Block -->
     <div class="barleft">
     <div class="barright">
     <p><b>Audit Log</b><span></span></p>
     </div>
     </div>
     <table align="center" cellpadding="5" cellspacing="1" width="95%" class="tbframe">
         <tr>
             <th>Timestamp</td>
             <th>User</td>
             <th>Event</td>
             <th>Description</td>
         </tr>
<?php 
     echo $this->partialLoop('remediation/log.tpl', $this->logs);
?>
</table>

