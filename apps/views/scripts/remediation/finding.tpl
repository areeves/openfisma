<!-- Heading Block -->
<div class="barleft">
<div class="barright">
<p><b>Finding Description</b><span><?php echo date('Y-M-D h:i:s:A');?></span>
</div>
</div>
<!-- End Heading Block -->

<br>

<!-- FINDING DETAIL TABLE -->
<table align="center" border="0" cellpadding="3" cellspacing="1" width="95%">

    <!-- finding and asset row -->
    <tr>

        <!-- finding information -->
        <td width="50%" valign="top">

            <!-- FINDING TABLE -->
            <table border="0" cellpadding="5" cellspacing="1" class="tipframe" width="100%">
                <th align="left" colspan="2">Finding Information</th>
                <tr><td><b>Finding ID:</b><?php echo $this->finding['f_id'];?></td></tr>
                <tr><td><b>Date Opened:</b><?php echo $this->finding['f_created'];?></td></tr>
                <tr><td><b>Finding Source:</b> (<?php echo $this->finding['fs_nickname'];?>)<?php echo $this->finding['fs_name'];?></td></tr>
                <tr><td><b>Finding Status:</b><?php echo $this->remediation_status;?></td></tr>
                <tr>
                    <td>
                        <b>Responsible System:</b>
                        <div id="system" type="select" name="remediation_owner"
                             option='{<?php foreach($this->system_list as $row){?>
                             "<?php echo $row['id'];?>":"<?php echo "(".$row['nickname'].")".$row['name'];?>",
                             <?php } ?> }'>
                             <span class="sponsor">
                             <?php
                                 if(isAllow('remediation','update_finding_assignment')){
                                     if('OPEN' == $this->remediation_status){ ?>
                                         <img src='/images/button_modify.png'></span>
                             <?php } } ?></span>
                             <span class="contenter"><?php echo "(".$this->remediation['system_nickname'].")".$this->remediation['system_name'];?></span>
                        </div>
                    </td>
                </tr>
            </table>
            <!-- FINDING TABLE -->
        </td>
        <!-- asset information -->
        <td width="50%" valign="top">
           <!-- ASSET TABLE -->
            <table border="0" cellpadding="5" cellspacing="1" class="tipframe" width="100%">
                <th align="left" colspan="2">Asset Information</th>
                <tr>
                    <td><b>Asset Owner:</b><?php echo '('.$this->finding['system_nickname'].')'.$this->finding['system_name'];?></td>
                </tr>
                <tr>
                    <td><b>Asset Name:</b>
                        <?php echo 'NULL'==$this->finding['asset_name']?'(none given)':$this->finding['asset_name'];?>
                    </td>
                </tr>
                <tr>
                    <td><b>Known Address(es):</b>
                        <?php foreach($this->asset_address as $asset){
                            echo '('.$asset['network_nickname'].')';
                            echo ''==$asset['ip']?'<i>(none_given)</i>':$asset['ip'];
                            echo ''==$asset['port']?'<i>(none given)</i>':$asset['port'];
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><b>Product Information:</b>
                         <?php if($this->product['prod_id'] == ""){ ?>
                            <i>(none given)</i>
                        <?php } else {
                            echo $this->product['prod_vendor'].$this->product['prod_name'].$this->product['prod_version'];
                        }?>
                    </td>
                </tr>
            </table>
            <!-- END ASSET TABLE -->
        </td>
    </tr>
    <tr> <!-- INSTANCE SPECIFIC DATA ROW -->
        <td colspan="2" width="90%">

            <!-- INSTANCE DATA TABLE -->
            <table border="0" cellpadding="5" cellspacing="1" class="tipframe" width="100%">
                <th align="left">Finding Description</th>
                <tr><td><?php echo ''==$this->finding['f_data']?'<i>(none given)</i>':$this->finding['f_data'];?></td></tr>
            </table>
            <!-- END INSTANCE DATA TABLE -->

        </td>
    </tr>
    <tr>
        <td colspan="2">

            <table border="0" cellpadding="5" cellspacing="1" width="100%" align="center" class="tipframe">
                <th align='left'>Additional Vulnerability Detail</th>
                <!-- VULNERABILITY ROW(S) -->
                <?php foreach($this->vulner as $row){ ?>
                <tr>
                    <td colspan="2">
                        <!-- VULERABILITIES TABLE -->
                        <table border="0" cellpadding="5" cellspacing="1" width="100%">
                            <tr><td><b>Vulnerability ID:</b><?php echo $row['type'].'-'.$row['seq'];?></td></tr>
                            <tr><td><b>Primary Description:</b><?php echo $row['primary'];?></td></tr>
                            <tr>
                                <td><b>Secondary Description:</b>
                                    <?php echo '0'==$row['secondary']?'<li>(none given)</li>':$row['secondary'];?>
                                </td>
                            </tr>
                        </table>
                        <!-- END VULERABILITIES TABLE -->
                    </td>
                </tr>
                <?php }?>
            </table>

        </td>
     </tr>
    <tr>
        <td colspan="2">
            <table cellpadding="5" width="100%" class="tipframe">
                <th align='left' colspan='2'>Recommendation</th>
                <tr>
                    <td colspan='2'>
                        <div id="recommendation" name="recommendation" type="textarea" rows="5" cols="160">
                        <?php if(isAllow('remediation','update_finding_recommendation')){?>
                        <span class="sponsor"><img src='/images/button_modify.png'></span> 
                        <?php }?>
                        <span class="contenter">
                            <?php echo $this->remediation['poam_action_suggested'];?>
                        </span></div>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>
<!-- END FINDING TABLE -->
    
