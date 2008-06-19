<form action="/zfentry.php/panel/remediation/sub/modify/id/<?php echo $this->poam['id'];?>" method="post">
<input type='submit' title='Save or Submit' value="Save" style="cursor: pointer;">
<?php 
     echo $this->partial('remediation/finding.tpl',
                          array('poam'=>&$this->poam, 'system_list' =>&$this->system_list));
     echo $this->partial('remediation/mitigation.tpl',  array('poam'    =>&$this->poam));
     echo $this->partial('remediation/nist.tpl', array('poam'=>&$this->poam));
?>
</form>

     <?php if($this->poam['status'] != 'NEW' || $this->poam['status'] != 'OPEN' ) { ?>
     <!-- Heading Block -->
     <div class="barleft">
     <div class="barright">
     <p><b>Supporting Evidence</b>(<?php echo count($this->ev_evals);?> total)<span></span></p>
     </div>
     </div>

     <?php 
          echo $this->partialLoop('remediation/evidence.tpl', $this->ev_evals );
          if( isAllow('remediation','update_evidence') ){
              echo 
              '<button id="up_evidence" onclick ="upload_evidence();">Upload Evidence</button>';
          }
     }
     ?>

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
<?php 
     echo $this->partial('remediation/uploadev.tpl',array('id'=>$this->poam['id']));
?>
