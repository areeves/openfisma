<!-- Heading Block -->
<div class="barleft">
<div class="barright">
<p><b>Finding Description</b><span><?php echo date('Y-M-D h:i:s:A');?></span>
</div>
</div>
<!-- FINDING DETAIL TABLE -->
<table align="center" border="0" cellpadding="3" cellspacing="1" width="95%">
    <!-- finding and asset row -->
    <tr>
        <!-- finding information -->
        <td width="50%" valign="top">
            <!-- FINDING TABLE -->
            <table border="0" cellpadding="5" cellspacing="1" class="tipframe" >
                <th align="left" colspan="2">Finding Information</th>
                <tr><td><b>POAM ID:</b><?php echo $this->poam['id'];?> 
                        <i>(Legacy Finding ID):</i>
                <?php echo $this->poam['legacy_finding_id'];?></td></tr>
                <tr><td><b>Date Opened:</b><?php echo $this->poam['create_ts'];?></td></tr>
                <tr><td><b>Source:</b> 
                (<?php echo $this->poam['source_nickname'];?>)
                <?php echo $this->poam['source_name'];?></td></tr>
                <tr><td><b>Status:</b>
                <?php 
                    $st = $this->poam['status'];
                    if( 'EN' == $st ) {
                        $date = new Zend_Date($this->poam['action_est_date']);
                        if( $date->isLater(Zend_Date::now()) ){
                            $st = 'EO';
                        }
                    }
                    echo $st;
                ?>
                </td></tr>
                <tr>
                    <td>
                        <b>Responsible System:</b>
                        <span name="poam[system_id]"
    <?php
        if('OPEN' == $this->poam['status'] && 
            isAllow('remediation','update_finding_assignment')){
            echo ' type="select" class="editable" 
                   href="/zfentry.php/metainfo/list/o/system/format/html/"';
        }
        echo '>',$this->system_list[$this->poam['system_id']];
    ?>
                        </span>
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
                    <td><b>Asset Owner:</b>
                    <?php echo $this->system_list[$this->poam['asset_owner']];?></td>
                </tr>
                <tr>
                    <td><b>Asset Name:</b>
                        <?php echo nullGet($this->poam['asset_name'],'(none given)');?>
                    </td>
                </tr>
                <tr>
                    <td><b>Known Address(es):</b>
                    <i><?php echo $this->network_list[$this->poam['network_id']],$this->poam['ip'],':',$this->poam['port']?> </i>
                    </td>
                </tr>
                <tr>
                    <td><b>Product Information:</b>
                    <i>
                        <?php  if( !empty($this->poam['product']) ){
                            echo $this->poam['product']['prod_vendor'].
                                 $this->poam['product']['prod_name'].
                                 $this->poam['product']['prod_version'];
                        }else{
                            echo '(not given)';
                        } ?>
                    </i>
                    </td>
                </tr>
            </table>
            <!-- END ASSET TABLE -->
        </td>
    </tr>
    </table>
<table border="0" cellpadding="5" cellspacing="1" class="tipframe" >
    <th align="left">Finding Description</th>
    <tr><td><i>
    <?php echo nullGet($this->poam['finding_data'],'(none given)'); ?></i>
    </td></tr>
</table>

<?php if( !empty($this->poam['vuln']) ) { ?>
<table border="0" cellpadding="5" cellspacing="1" class="tipframe">
    <th align='left'>Additional Vulnerability Detail</th>
    <!-- VULNERABILITY ROW(S) -->
    <?php foreach($this->poam['vuln'] as $row){ ?>
    <tr>
        <td colspan="2">
            <!-- VULERABILITIES TABLE -->
            <table border="0" cellpadding="5" cellspacing="1" width="100%">
                <tr><td><b>Vulnerability ID:</b><?php echo $row['type'].'-'.$row['seq'];?></td></tr>
                <tr><td><b>Description:</b><?php echo $row['description'];?></td></tr>
            </table>
            <!-- END VULERABILITIES TABLE -->
        </td>
    </tr>
    <?php }?>
</table>
<?php } ?>

<table cellpadding="5" width="100%" class="tipframe">
    <th align='left' colspan='2'>Recommendation</th>
    <tr>
        <td colspan='2'>
            <span name="poam[action_suggested]"
            <?php 
                if(isAllow('remediation','update_finding_recommendation')){
                    echo ' type="textarea" rows="5" cols="160" class="editable" ';
                }
                echo '>',$this->poam['action_suggested'];
            ?>
            </span>
        </td>
    </tr>
</table>
