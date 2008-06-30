<?php
    $year_list = array(0     =>'All Fiscal Year',
                       '2005' =>'2005',
                       '2006' =>'2006',
                       '2007' =>'2007',
                       '2008' =>'2008',
                       '2009' =>'2009');
    $overdue['type'] = array( 'sso' =>'SSO Approval overdue',
                         'action'   =>'Course of Action overdue');
    $overdue['day'] = array('1' => '0-29 days',
                            '2' => '30-59 days',
                            '3' => '60-89 days',
                            '4' => '90-119 days',
                            '5' => '120 and greater days');
    $this->system_list[0] ='--Any--';
    $this->source_list[0] ='--Any--';
    $url = "/zfentry.php/panel/report/sub/overdue/s/search";
?>
<div class="barleft">
<div class="barright">
<p><b>POA&amp;M Overdue Reports</b>
</div>
</div>

<form name="overdue_report" method="post" action="/zfentry.php/panel/report/sub/overdue/s/search">
<table width="95%" align="center" border="0" cellpadding="5" cellspacing="1" class="tipframe">
    <tr>
        <td width="6%" height="47"><b>System </b></td>
        <td width="21%">
        <?php echo $this->formSelect('system_id',
                                     nullGet($this->criteria['system_id'],0), 
                                     null,$this->system_list);
              if( !empty($this->criteria['system_id']) ) {
                  $url .= '/system_id/'.$this->criteria['system_id'];
              }
        ?>
        </td>
        <td width="6%"><b>Source</b></td>
        <td width="18%">
        <?php echo $this->formSelect('source_id',
                                     nullGet($this->criteria['source_id'],0), 
                                     null,$this->source_list);
              if( !empty($this->criteria['source_id']) ) {
                  $url .= '/source_id/'.$this->criteria['source_id'];
              }
        ?>
        </td>
        <td width="9%"><b>Fiscal Year</b></td>
        <td width="40%">
        <?php echo $this->formSelect('year',
                                     nullGet($this->criteria['year'],0), 
                                     null,$year_list);
              if( !empty($this->criteria['year']) ) {
                  $url .= '/year/'.$this->criteria['year'];
              }
        ?>
        </td>
    </tr>
    <tr>
        <td><b>Overdue type</b></td>
        <td >
        <?php echo $this->formSelect('overdue[type]',
                                    nullGet($this->criteria['overdue']['type'],0),
                                    null,$overdue['type']);
              if( !empty($this->criteria['overdue']['type']) ) {
                  $url .= '/overdue[type]/'.$this->criteria['overdue']['type'];
              }
        ?>
        </td>
        <td><b>Overdue </b></td>
        <td> 
        <?php echo $this->formSelect('overdue[day]',
                                    nullGet($this->criteria['overdue']['day'],0),
                                    null,$overdue['day']);
              if( !empty($this->criteria['overdue']['day']) ) {
                  $url .= '/overdue[day]/'.$this->criteria['overdue']['day'];
              }
        ?>
        </td>
    </tr>
    <tr>
        <td height="39" colspan="2"><input type="submit" name="search" value="Generate"></td>
    </tr>
</table>
</form>



<?php if( !empty($this->poam_list) ) { ?>

<div class="barleft">
<div class="barright">
<p><b>Poam Search Results</b>
    <span>
    <?php echo $this->links['all']; ?>
    <a target='_blank' href="<?php echo $url.'/format/pdf'; ?>"><img src="/images/pdf.gif" border="0"></a>
    <a href="<?php echo $url.'/format/xls'; ?>"><img src="/images/xls.gif" border="0"></a>
    </span>
</div>
</div>
<table width="95%" align="center" border="0" cellpadding=5" cellspacing="0" class="tbframe">
    <tr align="center">
        <th class="tdc">System</th>
        <th class="tdc">ID#</th>
        <th class="tdc">Description</th>
        <th class="tdc">Type</th>
        <th class="tdc">Status</th>
        <th class="tdc">Source</th>
        <th class="tdc">Server/Database</th>
        <th class="tdc">Location</th>
        <th class="tdc">Risk Level</th>
        <th class="tdc">Recommendation</th>
        <th class="tdc">Corrective Action</th>
        <th class="tdc">ECD</th>
    </tr>
    <?php foreach($this->poam_list as  $row){ ?>
    <tr>
        <td class="tdc" align="center"><?php echo $this->system_list[$row['system_id']];?></td>
        <td class="tdc" align="center"><?php echo $row['id'];?></td>
        <td class="tdc"><?php echo $row['finding_data'];?></td>
        <td class="tdc" ><?php echo $row['type'];?></td>
        <td class="tdc" align="center"><?php echo $row['status'];?></td>
        <td class="tdc" align="center">
        <?php $id = &$row['source_id'];
            if( empty($id) ){
                echo 'N/A';
            }else{ 
                echo $this->source_list[$id];
            }
        ?>
        </td>
        <td class="tdc"><?php echo $row['ip'];?></td>
        <td class="tdc" >
        <?php $id = &$row['network_id'];
            if( empty($id) ){
                echo 'N/A';
            }else{ 
                echo $this->network_list[$id];
            }
        ?>
        </td>
        <td class="tdc" align="center"><?php echo $row['threat_level'];?></td>
        <td class="tdc"><?php echo $row['action_suggested'];?></td>
        <td class="tdc"><?php echo $row['action_planned'];?></td>
        <td class="tdc" align="center"><?php echo $row['action_est_date'];?></td>
    </tr>
    <?php } ?>
</table>

<?php } ?>
