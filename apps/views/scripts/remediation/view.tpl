<script language="javascript">
<!--
function go(step) {
    document.finding.action.value = step;
    document.finding.submit();
}
-->
</script>
<script LANGUAGE="JavaScript" type="text/javascript" src="javascripts/jquery/remediation_edit.js"></script>
<br>
<table width="95%" border="0" align="center">
<tr>
<td>
<table border="0" align="left">
    <tr>
        <td>
            <!-- SAVE MODIFICATIONS TO REMEDIATION -->
            <form action='remediation_modify.php' method='POST'>
                <input type='hidden' name='action'         value='add'>
                <input type='hidden' name='validated'      value='no'>
                <input type='hidden' name='approved'       value='no'>
                <input type='hidden' name='target'         value='save_poam'>
                <input type='hidden' name='remediation_id' value='<?php echo $this->remediation_id;?>'>
                <input type='hidden' name='form_action' value=''>
                <input type='submit' title='Save or Submit' value="Save" style="cursor: pointer;">
            </form>
        </td>
        <td>
            <!-- RETURN TO THE SUMAMRY LIST -->
            <form action='remediation.php' method='POST'>
                <input type='hidden' name='remediation_ids'        value='{$remediation_ids}'>
                <input type='hidden' name='filter_source'          value='{$filter_source}'>
                <input type='hidden' name='filter_system'          value='{$filter_system}'>
                <input type='hidden' name='filter_status'          value='{$filter_status}'>
                <input type='hidden' name='filter_type'            value='{$filter_type}'>
                <input type='hidden' name='filter_startdate'       value='{$filter_startdate}'>
                <input type='hidden' name='filter_enddate'         value='{$filter_enddate}'>
                <input type='hidden' name='filter_startcreatedate' value='{$filter_startcreatedate}'>
                <input type='hidden' name='filter_endcreatedate'   value='{$filter_endcreatedate}'>
                <input type='hidden' name='filter_asset_owners'    value='{$filter_asset_owners}'>
                <input type='hidden' name='filter_action_owners'   value='{$filter_action_owners}'>
                <input type='hidden' name='form_action' value='Return to Summary List'>
                <input name="button" type="submit" id="button" value="Go Back" style="cursor: pointer;">
            </form>
        </td>
    </tr>
</table>
</td>
</tr>
</table>

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
                        <form action='remediation_modify.php' method='POST'>
                        <input type='hidden' name='remediation_id' value='<?php echo $this->remediation_id;?>'>
                        <input type='hidden' name='root_comment'   value='<?php echo $this->root_comment;?>'>
                        <input type='hidden' name='target'         value='remediation_owner'>
                        <input type='hidden' name='action'         value='update'>
                        <input type='hidden' name='validated'      value='no'>
                        <input type='hidden' name='approved'       value='no'>

                        <b>Responsible System:</b>
                        <?php
                        if(isAllow('remediation','update_finding_assignment')){
                            if('OPEN' == $this->finding['f_status']){ ?> 
                        <input type='hidden' name='form_action' value='Update'>
                        <input type='image' src='/images/button_modify.png' name='form_action' value='Update'> 
                        <?php } } ?>
                        <span>(<?php echo $this->finding['system_nickname'];?>) <?php echo $this->finding['system_name'];?></span>
                        </form>

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
                        <form action='remediation_modify.php' method='POST'>
                        <input type='hidden' name='remediation_id' value='<?php echo $this->remediation_id;?>'>
                        <input type='hidden' name='root_comment'   value='<?php echo $this->root_comment;?>'>
                        <input type='hidden' name='target'         value='action_suggested'>
                        <input type='hidden' name='action'         value='update'>
                        <input type='hidden' name='validated'      value='no'>
                        <input type='hidden' name='approved'       value='no'>

                        <?php if(isAllow('remediation','update_finding_recommendation')){?>
                        <input type='hidden' name='form_action' value='Update'>
                        <input type='image' src='/images/button_modify.png' name='form_action' value='Update'>
                        <?php }?>
                        <span><?php echo $this->remediation['poam_action_suggested'];?></span>
                        </form>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table> 
<!-- END FINDING TABLE -->
<br>
<!-- Heading Block -->
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="13"><img src="/images/left_circle.gif" border="0"></td>
        <td bgcolor="#DFE5ED"><b>Mitigation Strategy</b></td>
        <td bgcolor="#DFE5ED" align="right"></td>
        <td width="13"><img src="/images/right_circle.gif" border="0"></td>
    </tr>
</table>
<!-- End Heading Block -->

<br>

<!-- MITIGATION STRATEGY -->
<table border="0" width="95%" align="center">
    <tr>
        <td colspan='2'>
            
            <!-- Course of Action Table -->
            <table width="100%" cellpadding="5" class="tipframe">
                <th align="left">Course of Action</th>

                <tr>
                    <td align="left">
    
                        <form action='remediation_modify.php' method='POST'>
                        <input type='hidden' name='remediation_id' value='<?php echo $this->remediation_id;?>'>
                        <input type='hidden' name='root_comment'   value='<?php echo $this->root_comment;?>'>
                        <input type='hidden' name='target'         value='remediation_type'>
                        <input type='hidden' name='action'         value='update'>
                        <input type='hidden' name='validated'      value='no'>
                        <input type='hidden' name='approved'       value='no'>
                
                        <b>Type:</b> 

                        <?php if(isAllow('remediation','update')){
                            if('OPEN' == $this->remediation['poam_status']){
                        ?>
                        <input type='hidden' name='form_action' value='Update'>
                        <input type='image' src='/images/button_modify.png' name='form_action' value='Update'>
                        <? } } ?>
                        <span><?php echo $this->remediation['poam_type'];?></span>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td>
                        <form action='remediation_modify.php' method='POST'>
                        <input type='hidden' name='remediation_id' value='<?php echo $this->remediation_id;?>'>
                        <input type='hidden' name='root_comment'   value='<?php echo $this->root_comment;?>'>
                        <input type='hidden' name='target'         value='action_planned'>
                        <input type='hidden' name='action'         value='update'>
                        <input type='hidden' name='validated'      value='no'>
                        <input type='hidden' name='approved'       value='no'>

                        <b>Description:</b> 
                        
                        <?php if(isAllow('remediation','update_finding_course_of_action')){
                            if('OPEN' == $this->remediation['poam_status']){
                        ?>
                        <input type='hidden' name='form_action' value='Update'>
                        <input type='image' src='/images/button_modify.png' name='form_action' value='Update'>
                        <? } }?>
                        <span><?php echo $this->remediation['poam_action_planned'];?></span>
                        </form>
                    
                    </td>
                </tr>
            </table>
            <!-- End Course of Action Table -->
            
        </td>
    </tr>
    <tr>
        <td colspan='2'>
        
            <!-- Resources Required for Course of Action Table -->
            <table width="100%" cellpadding="5" class="tipframe">
                <th align="left">Resources Required for Course of Action</th>
                <tr>
                    <td>

                        <form action='remediation_modify.php' method='POST'>
                        <input type='hidden' name='remediation_id' value='<?php echo $this->remediation_id;?>'>
                        <input type='hidden' name='root_comment'   value='<?php echo $this->root_comment;?>'>
                        <input type='hidden' name='target'         value='action_resources'>
                        <input type='hidden' name='action'         value='update'>
                        <input type='hidden' name='validated'      value='no'>
                        <input type='hidden' name='approved'       value='no'>

                        <?php if(isAllow('remediation','update_finding_resources')){?>
                        <input type='hidden' name='form_action' value='Update'>
                        <input type='image' src='/images/button_modify.png' name='form_action' value='Update'>
                        <? } ?>
                        <span><?php echo $this->remediation['poam_action_resources'];?></span>
                        </form>
                        </td>
                </tr>
            </table>
            <!-- End Resources Required for Course of Action Table -->
        
        </td>
    </tr>
        
    <tr>
        <td width='50%'>
            <form action='remediation_modify.php' method='POST'>
            <input type='hidden' name='remediation_id' value='<?php echo $this->remediation_id;?>'>
            <input type='hidden' name='root_comment'   value='<?php echo $this->root_comment;?>'>
            <input type='hidden' name='target'         value='action_date_est'>
            <input type='hidden' name='action'         value='update'>
            <input type='hidden' name='validated'      value='no'>
            <input type='hidden' name='approved'       value='no'>

            <b>Estimated Completion Date:</b> 

            <?php if(isAllow('remediation','update_est_completion_date')){
                if('OPEN' == $this->remediation['poam_status']){
            ?>
            <input type='hidden' name='form_action' value='Update'>
            <input type='image' src='/images/button_modify.png' name='form_action' value='Update'>
            <?php } }?>
            <span><?php echo $this->remediation['poam_action_date_est'];?></span>
            </form>
        </td>
        <td width='50%'>
            <b>Actual Completion Date:</b>
            <?php echo '' != $this->remediation['poam_action_date_actual']?$this->remediation['poam_action_date_actual']:'<i>(action not yet completed)</i>';?> 
        </td>
    </tr>
    <?php if($this->num_comments_est > 0){ ?>
	<tr>
		<td colspan='2'>
			<!-- Comments for ECD Modification Table -->
			<table width="100%" border="0" cellpadding="5" class="tipframe">
				<th align="left">Comments For Date Modification <i>(<?php echo $this->num_comments_est;?> total)</i></th>
				<tr>
					<td>
						<!-- COMMENT TABLE -->
						<table border="1" align="left" cellpadding="5" cellspacing="1" width="100%" class="tbframe">
							<tr>
								<th nowrap>Changed On</td>
								<th nowrap>Changed By</td>
								<th nowrap>Reason for Change</td>
							</tr>
                            <?php foreach($this->comments_est as $row){ ?>
							<tr>
								<td class="tdc" nowrap><?php echo $row['comment_date'];?></td>
								<td class="tdc" nowrap><?php echo $row['user_name'];?></td>
								<td class="tdc"><?php echo $row['comment_body'];?></td>
							</tr>
							<?php } ?>
						</table>
						<!-- COMMENT TABLE -->
					</td>
				</tr>
			</table>
			<!-- End Comments for ECD Modification Table -->
		</td>
	</tr>
    <?php } ?>
</table>
<!-- END MITIGATION STRATEGY TABLE -->
<br>
<!-- Heading Block -->
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="13"><img src="/images/left_circle.gif" border="0"></td>
		<td bgcolor="#DFE5ED"><b>NIST 800-53 Control Mapping</b></td>
		<td bgcolor="#DFE5ED" align="right"></td>
		<td width="13"><img src="/images/right_circle.gif" border="0"></td>
	</tr>
</table>
<!-- End Heading Block -->
<br>
    <?php if((!empty($this->blscr) && $this->blscr['blscr_number'] == '')|| empty($this->blscr)){ ?>
		<!-- BLSCR TABLE -->
   		<table border="1" width="95%" align="center" cellpadding="5" cellspacing="1" class="tipframe">
	       	<th align="left" >Security Control</th>
			<tr><td><i>(none given)</i></td></tr>
			<tr>
				<td>
					<form action='remediation_modify.php' method='POST'>
						<input type='hidden' name='remediation_id' value='<?php echo $this->remediation_id;?>'>
						<input type='hidden' name='root_comment'   value='<?php echo $this->root_comment;?>'>
						<input type='hidden' name='target' 		   value='blscr_number'>
						<input type='hidden' name='action'         value='update'>
						<input type='hidden' name='validated'      value='no'>
						<input type='hidden' name='approved'       value='no'>
					
					<b>Number:</b>
					
						<?php if(isAllow('remediation','update_control_assignment')){ ?>
							<input type='hidden' name='form_action' value='Update'>
							<input type='image' src='/images/button_modify.png' name='form_action' value='Update'>
						<?php } ?>
					</form>
				</td>
			</tr>
		</table>
	<?php } 
        if(!empty($this->blscr) && ($this->blscr['blscr_number'] != "")){ ?>
   		<table border="0" width="95%" align="center" cellpadding="5" class="tipframe">
			<tr>
				<td>
			<table align="left" border="0" cellpadding="5" class="tbframe">
				<tr>
					<th class="tdc">Control Number</th>
					<th class="tdc">Class</th>
					<th class="tdc">Family</th>
					<th class="tdc">Subclass</th>
					<th class="tdc">Low</th>
					<th class="tdc">Moderate</th>
					<th class="tdc">High</th>
				</tr>
				<tr>
					<td class="tdc" align="center">
						<form action='remediation_modify.php' method='POST'>
							<input type='hidden' name='remediation_id' value='<?php echo $this->remediation_id;?>'>
							<input type='hidden' name='root_comment'   value='<?php echo $this->root_comment;?>'>
							<input type='hidden' name='target' 		   value='blscr_number'>
							<input type='hidden' name='action'         value='update'>
							<input type='hidden' name='validated'      value='no'>
							<input type='hidden' name='approved'       value='no'>
                            <?php if(isAllow('remediation','update_control_assignment')){ ?>
								<input type='hidden' name='form_action' value='Update'>
								<input type='image' src='/images/button_modify.png' name='form_action' value='Update'>
							<?php } ?>
							<span><?php echo $this->blscr['blscr_number'];?></span>
						</form>
					</td>
					<td class="tdc"><?php echo $this->blscr['blscr_class'];?></td>
					<td class="tdc"><?php echo $this->blscr['blscr_family'];?></td>
					<td class="tdc"><?php echo $this->blscr['blscr_subclass'];?></td>	
					<td class="tdc" align="center">
                        <?php echo 1 == $this->blscr['blscr_low']?'Control Required':'Control Not Required';?>
                    </td>
					<td class="tdc" align="center">
                        <?php echo 1 == $this->blscr['blscr_moderate']?'Control Required':'Control Not Required';?>
                    </td>
					<td class="tdc" align="center">
                        <?php echo 1 == $this->blscr['blscr_high']?'Control Required':'Control Not Required';?>
                    </td>
				</tr>
			</table>
				</td>
			</tr>	
			<tr><td><b>Control: </b> <?php echo $this->blscr['blscr_control'];?></td></tr>
		    <tr><td><b>Guidance: </b> <?php echo $this->blscr['blscr_guidance'];?></td></tr>
		    <tr><td><b>Enhancements: </b>
                <?php '.' == $this->blscr['blscr_enhancements']?'<i>(none given)</i>':$this->blscr['blscr_enhancements'];?>
            </td></tr>
	    	<tr><td><b>Supplement: </b>
                <?php '.' == $this->blscr['blscr_supplement']?'<i>(none given)</i>':$this->blscr['blscr_supplement'];?>
            </td></tr>
		</table>
	<?php } ?>
<br>
<!-- Heading Block -->
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="13"><img src="/images/left_circle.gif" border="0"></td>
		<td bgcolor="#DFE5ED"><b>Risk Analysis</b></td>
		<td bgcolor="#DFE5ED" align="right"></td>
		<td width="13"><img src="/images/right_circle.gif" border="0"></td>
	</tr>
</table>
<!-- End Heading Block -->
<br>
<!-- REMEDIATION TABLE -->
<table border="0" cellpadding="5" cellspacing="1" width="95%" align="center">
	<tr> <!-- REMEDIATION INFORMATION ROW -->
		<td width='50%' valign='top'>
			<table border="0" cellpadding="5" cellspacing="1" width="100%" class="tipframe">
				<th align="left" colspan='2'>Risk Analysis Form</th>
				<tr>
					<td>
						Based on the guidance provided by NIST Special Publication 800-37, to derive an overall likelihood rating that indicates the probability that a potential vulnerability may be exercised, we must first define the threat-source motivation and capability while considering the nature of the vulnerability and the existence and effectiveness of current controls or countermeasures. The following two sections on Threat Information and Countermeasure Information will help us define the iformation required to generate a threat likelihood risk level which will be used to generate the overall risk level of this vulnerability as it pertains to your information system.
					</td>
				</tr>
				<tr>
						<!--RESTRICT BY ROLE -->
                        <?php if(isAllow('remediation','generate_raf')){ ?>
						<!-- CHECK THAT CMEASURE AND THREAT LEVEL ARE SET-->
                        <?php if(($this->threat_level != 'NONE') && ($this->cmeasure_effectiveness != 'NONE')){ ?> 
						<form action='raf.php' method='POST' target='_blank'>
						<input type='hidden' name='poam_id'     value='<?php echo $this->remediation_id;?>'>
					<td colspan='2'>
						<input type='hidden' name='form_action' value='Generate RAF'>
						<input type='submit' name='form_action' value='Generate RAF'>
					</td>
						</form>
						<?php } else { ?>
					<td colspan='2'><i>(Threat and Countermeasure information must be completed to generate a RAF)</i></td>
						<?php } ?>
						<?php } else { ?>
					<td colspan='2'>&nbsp;</td>
						<?php } ?>
				</tr>
			</table>
		</td>
    </tr>
    <tr> <!-- THREATS ROW -->
        <td colspan="2">
            <!-- THREATS TABLE -->
            <table border="0" cellpadding="5" cellspacing="1" class="tipframe" width="100%">
                <th align='left'>Threat Information</th>
                    <tr>
                        <td>
                            A threat is the potential for a particular threat-source to successfully exercise a particular vulnerability. A vulnerability is a weakness that can be accidentally triggered or intentionally exploited. A threat-source does not present a risk when there is no vulnerability that can be exercised. In determining the likelihood of a threat, one must consider threat-sources, potential vulnerabilities, and existing controls. Common threat sources are: (1) Natural Threats: Floods, earthquakes, tornadoes, landslides, avalanches, electrical storms, and other such events, (2) Human Threats: Events that are either enabled by or caused by human beings, such as unintentional acts (inadvertent data entry) or deliberate actions (network based attacks, malicious software upload, unauthorized access to confidential information), and (3) Environmental Threats: Long-term power failure, pollution, chemicals, liquid leakage.
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <form action='remediation_modify.php' method='POST'>
                            <input type='hidden' name='remediation_id' value='<?php echo $this->remediation_id;?>'>
                            <input type='hidden' name='root_comment'   value='<?php echo $this->root_comment;?>'>
                            <input type='hidden' name='target'         value='threat_level'>
                            <input type='hidden' name='action'         value='update'>
                            <input type='hidden' name='validated'      value='no'>
                            <input type='hidden' name='approved'       value='no'>
                            <b>Level:</b> 
        
                            <?php if(isAllow('remediation','update_threat')){ ?>
                            <input type='hidden' name='form_action' value='Update'>
                            <input type='image' src='/images/button_modify.png' name='form_action' value='Update'>
                            <?php } ?>
                            <span><?php echo $this->threat_level;?></span>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <form action='remediation_modify.php' method='POST'>
                            <input type='hidden' name='remediation_id' value='<?php echo $this->remediation_id;?>'>
                            <input type='hidden' name='root_comment'   value='<?php echo $this->root_comment;?>'>
                            <input type='hidden' name='target'         value='threat_source'>
                            <input type='hidden' name='action'         value='update'>
                            <input type='hidden' name='validated'      value='no'>
                            <input type='hidden' name='approved'       value='no'>
                            <b>Source:</b> 
                            
                            <?php if(isAllow('remediation','update_threat')){ ?>
                            <input type='hidden' name='form_action' value='Update'>
                            <input type='image' src='/images/button_modify.png' name='form_action' value='Update'>
                            <?php } ?>
                            <span><?php echo $this->remediation['poam_threat_source'];?></span>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <form action='remediation_modify.php' method='POST'>
                            <input type='hidden' name='remediation_id' value='<?php echo $this->remediation_id;?>'>
                            <input type='hidden' name='root_comment'   value='<?php echo $this->root_comment;?>'>
                            <input type='hidden' name='target'         value='threat_justification'>
                            <input type='hidden' name='action'         value='update'>
                            <input type='hidden' name='validated'      value='no'>
                            <input type='hidden' name='approved'       value='no'>

                            <b>Justification:</b> 

                            <?php if(isAllow('remediation','update_threat')){ ?>
                            <input type='hidden' name='form_action' value='Update'>
                            <input type='image' src='/images/button_modify.png' name='form_action' value='Update'>
                            <?php } ?>

                            <span><?php echo $this->remediation['poam_threat_justification'];?></span>
                            </form>
                        </td>
                    </tr>
                </table> 
                <!-- END THREATS TABLE -->
            </td>
        </tr>
    <tr> <!-- COUNTERMEASURES ROW -->
        <td colspan="2">
            <!-- COUNTERMEASURE TABLE -->
            <table border="0" cellpadding="5" cellspacing="1" class="tipframe" width="100%">
                <th align="left" colspan="2">Countermeasure Information</th>
                    <tr>
                        <td>
                            The goal of this step is to analyze the controls that have been implemented, or are planned for implementation, by the organization to minimize or eliminate the likelihood (or probability) of a threat's exercising a system vulnerability. Countermeasures or Security controls encompass the use of technical and nontechnical methods. Technical controls are safeguards that are incorporated into computer hardware, software, or firmware (e.g., access control mechanisms, identification and authentication mechanisms, encryption methods, intrusion detection software). Nontechnical controls are management and operational controls, such as security policies; operational procedures; and personnel, physical, and environmental security.
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <form action='remediation_modify.php' method='POST'>
                            <input type='hidden' name='remediation_id' value='<?php echo $this->remediation_id;?>'>
                            <input type='hidden' name='root_comment'   value='<?php echo $this->root_comment;?>'>
                            <input type='hidden' name='target'         value='cmeasure_effectiveness'>
                            <input type='hidden' name='action'         value='update'>
                            <input type='hidden' name='validated'      value='no'>
                            <input type='hidden' name='approved'       value='no'>

                            <b>Effectiveness:</b> 

                            <!-- RESTRICT UPDATE BASED ON STATUS AND ROLE-->
                            <?php if(isAllow('remediation','update_cmeasures')){ ?>
                            <input type='hidden' name='form_action' value='Update'>
                            <input type='image' src='/images/button_modify.png' name='form_action' value='Update'>
                            <?php } ?>
                            <span><?php echo $this->cmeasure_effectiveness;?></span>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <form action='remediation_modify.php' method='POST'>
                            <input type='hidden' name='remediation_id' value='<?php echo $this->remediation_id;?>'>
                            <input type='hidden' name='root_comment'   value='<?php echo $this->root_comment;?>'>
                            <input type='hidden' name='target'         value='cmeasure'>
                            <input type='hidden' name='action'         value='update'>
                            <input type='hidden' name='validated'      value='no'>
                            <input type='hidden' name='approved'       value='no'>

                            <b>Countermeasure:</b> 

                            <!--RESTRICT UPDATE BASED ON STATUS AND ROLE-->
                            <?php if(isAllow('remediation','update_cmeasure')){ ?>
                            <input type='hidden' name='form_action' value='Update'>
                            <input type='image' src='/images/button_modify.png' name='form_action' value='Update'>
                            <?php } ?>
                            <span><?php echo $this->remediation['poam_cmeasure'];?></span>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <form action='remediation_modify.php' method='POST'>
                            <input type='hidden' name='remediation_id' value='<?php echo $this->remediation_id;?>'>
                            <input type='hidden' name='root_comment'   value='<?php echo $this->root_comment;?>'>
                            <input type='hidden' name='target'         value='cmeasure_justification'>
                            <input type='hidden' name='action'         value='update'>
                            <input type='hidden' name='validated'      value='no'>
                            <input type='hidden' name='approved'       value='no'>
    
                            <b>Justification:</b> 
    
                            <!--RESTRICT UPDATE BASED ON STATUS AND ROLE-->
                            <?php if(isAllow('remediation','udpate_cmeasure')){ ?>
                            <input type='hidden' name='form_action' value='Update'>
                            <input type='image' src='/images/button_modify.png' name='form_action' value='Update'>
                            <?php } ?>
                            <span><?php echo $this->remediation['poam_cmeasure_justification'];?></span>
                            </form>
                    </td>
                </tr>
            </table> 
            <!-- END COUNTERMEASURE TABLE -->
        </td>
    </tr>

    <tr>

        <td colspan='2'>
        <table border="0" cellpadding="5" cellspacing="1" width="100%" class="tipframe">

            <th align='left'>Approval</th>
            <tr>
                <td colspan="2">
                    <i>(All fileds above must be set and saved to make SSO approval field editable.)</i>
                </td>
            </tr>
            <tr>

                <td colspan='2'>
            <form action='remediation_modify.php' method='POST'>
            <input type='hidden' name='remediation_id' value='<?php echo $this->remediation_id;?>'>
            <input type='hidden' name='root_comment'   value='<?php echo $this->root_comment;?>'>
            <input type='hidden' name='target'         value='action_approval'>
            <input type='hidden' name='action'         value='update'>
            <input type='hidden' name='validated'      value='no'>
            <input type='hidden' name='approved'       value='no'>

                    <b>SSO Approval:</b>

                    <!--RESTRICT UPDATE BASED ON STATUS AND ROLE-->
                    <?php if(isAllow('remediation','update_mitigation_strategy_approval')){
                        if(($this->remediation_type != 'NONE') && ($this->is_completed == 'yes')){
                            if(($this->remediation_status == 'OPEN') || ($this->remediation_status == 'EN') || ($this->remediation_status == 'EO')){ ?>

                        <input type='hidden' name='form_action' value='Update'>
                        <input type='image' src='/images/button_modify.png' name='form_action' value='Update'>

                    <?php }  }  } ?>
                   <span><?php echo $this->remediation['poam_action_status'];?></span>
            </form>
                </td>

            </tr>
            <?php if($this->num_comments_sso > 0){ ?>
            <tr><th align="left" colspan="2">Comments From SSO <i>(<?php echo $this->num_comments_sso;?>total)</i></th></tr>
            <tr>
                <td colspan="2" width="90%">
                    <table border="1" cellpadding="5" cellspacing="0" width="100%" class="tbframe">
                        <th nowrap>Comment On</th>
                        <th nowrap>Comment By</th>
                        <th nowrap>Event</th>
                        <th nowrap>Description</th>
                        
                        <?php foreach($this->comments_sso as $row){ ?>
                        <tr>
                            <td nowrap><?php echo $row['comment_date'];?></td>
                            <td nowrap><?php echo $row['user_name'];?></td>
                            <td><?php echo $row['comment_topic'];?></td>
                            <td><?php echo $row['comment_body'];?></td>
                        </tr>
                        <?php } ?>
                    </table>
                </td>
            </tr>
            <?php } ?>
        </table>
        </td>
    </tr>
</table> <!-- REMEDIATION TABLE -->
<br>

<!-- ------------------------------------------------------------------------ -->
<table width="95%" border="0" align="center">
<tr>
<td>
<table border="0" align="left">
    <tr>
        <td>
            <!-- SAVE MODIFICATIONS TO REMEDIATION -->
            <form action='remediation_modify.php' method='POST'>
                <input type='hidden' name='action'         value='add'>
                <input type='hidden' name='validated'      value='no'>
                <input type='hidden' name='approved'       value='no'>
                <input type='hidden' name='target'         value='save_poam'>
                <input type='hidden' name='remediation_id' value='<?php echo $this->remediation_id;?>'>
                <input type='hidden' name='form_action' value=''>
                <input type='submit' title='Save or Submit' value="Save" style="cursor: pointer;">
            </form>
        </td>
        <td>
            <!-- RETURN TO THE SUMAMRY LIST -->
            <form action='remediation.php' method='POST'>
                <input type='hidden' name='remediation_ids'        value='{$remediation_ids}'>
                <input type='hidden' name='filter_source'          value='{$filter_source}'>
                <input type='hidden' name='filter_system'          value='{$filter_system}'>
                <input type='hidden' name='filter_status'          value='{$filter_status}'>
                <input type='hidden' name='filter_type'            value='{$filter_type}'>
                <input type='hidden' name='filter_startdate'       value='{$filter_startdate}'>
                <input type='hidden' name='filter_enddate'         value='{$filter_enddate}'>
                <input type='hidden' name='filter_startcreatedate' value='{$filter_startcreatedate}'>
                <input type='hidden' name='filter_endcreatedate'   value='{$filter_endcreatedate}'>
                <input type='hidden' name='filter_asset_owners'    value='{$filter_asset_owners}'>
                <input type='hidden' name='filter_action_owners'   value='{$filter_action_owners}'>
                <input type='hidden' name='form_action' value='Return to Summary List'>
                <input name="button" type="submit" id="button" value="Go Back" style="cursor: pointer;">
            </form>
        </td>
    </tr>
</table>
</td>
</tr>
</table>

<!-- NO REAL NEED TO SHOW UNTIL EN, EO, EP, ES or CLOSED-->
<?php if(isAllow('remediation','read_evidence')){
    //-- Statement 1 --
    if($this->remediation_status != 'OPEN'){
    // -- Statement 2 --
?>
<!-- Heading Block -->
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="13"><img src="/images/left_circle.gif" border="0"></td>
        <td bgcolor="#DFE5ED"><b>Supporting Evidence</b></td>
        <td bgcolor="#DFE5ED" align="right"></td>
        <td width="13"><img src="/images/right_circle.gif" border="0"></td>
    </tr>
</table>
<!-- End Heading Block -->

<br>

<!-- EVIDENCE TABLE -->
<table border="0" cellpadding="5" cellspacing="1" width="95%" align="center" class="tipframe">

    <th align='left' colspan="2">Evidence Submissions <i>(<?php echo $this->num_evidence;?> total)</i></th>
    
        <!--loop through the evidence-->
        <?php if($this->num_evidence > 0 ){ 
            //-- Statement 3 -->
            foreach($this->all_evidence as $row){
                //DO NOT SHOW BAD EVIDENCE AT STATUS ES
                if(($this->remediation_status == 'ES')&& ($row['ev_sso_evaluation'] != 'APPROVED') ||($this->remediation_status == 'ES') && ($row['ev_fsa_evaluation'] != 'APPROVED')){
                }
                else {
        ?>
    <tr>
        <!-- EVIDENCE TABLE-->
        <td colspan='2' width='100%'>
            <table border='0' cellpadding='3' cellspacing='1' class='tipframe' width='100%'>
                <tr><th align='left' colspan="2">Evidence Submitted by <?php echo $row['submitted_by'];?> on <?php echo $row['ev_date_submitted'];?></th></tr>
                <tr colspan="2">
                    <td><b>Evidence:</b>
                    <?php if($row['fileExists'] == 1){ ?>
                    <a href="javascript:void(0)" onClick="window.open('<?php echo $row['ev_submission'];?>', 'evidence_window', config='resizable=yes,menubar=no,scrollbars=yes')"><?php echo $row['fileName'];?></a>
                    <?php } else { echo $row['fileName'];} ?></td>
                </tr>
                <!-- SSO EVALUATION -->
                <tr>

                <!-- RESTRICT UPDATE BASED ON STATUS AND ROLE-->
                <?php if((isAllow('remediation','update_evidence_approval_first')) && ($this->remediation_status == 'EP') && ($row['ev_sso_evaluation'] == 'NONE')){
                ?>
                    <td>
                        <form action='remediation_modify.php' method='POST'>
                            <input type='hidden' name='remediation_id' value='<?php echo $this->remediation_id;?>'>
                            <input type='hidden' name='ev_id'          value='{$all_evidence[row].ev_id}'>
                            <input type='hidden' name='root_comment'   value='<?php echo $this->root_comment;?>'>
                            <input type='hidden' name='target'         value='evidence'>
                            <input type='hidden' name='action'         value='sso_evaluate'>
                            <input type='hidden' name='validated'      value='no'>
                            <input type='hidden' name='approved'       value='no'>
        
                        <b>ISSO Evaluation:</b> 
                            <input type='hidden' name='form_action' value='Evaluate'>
                            <input type='image' src='/images/button_modify.png' name='form_action' value='Evaluate'> 
                            <span><?php echo $row['ev_sso_evaluation'];?></span>
                            </form>
                    </td>
                <?php } else { ?>
                    <td><b>ISSO Evaluation:</b><?php echo $row['ev_sso_evaluation'];?></td>
                    <?php if(isset($row['comments']['EV_SSO']) && $row['comments']['EV_SSO'] != ''){ ?>
                    <td width="85%">
                        <table border="0" cellpadding="3" cellspacing="1" class="tipframe" width="100%">
                            <tr><th align='left'><?php echo $row['comments.EV_SSO.comment_topic'];?></th></tr>
                            <tr><td ><?php echo $row['comments.EV_SSO.comment_body'];?></td></tr>
                            <tr><td align='right'><i><?php echo $row['comments.EV_SSO.comment_date'];?>by<?php echo $row['comments.EV_SSO.user_name'];?></i></td></tr>
                        </table>
                    </td>
                    <?php } } ?>
                </tr>
                <!--FSA EVALUATION-->
                <tr>
                <!--RESTRICT UPDATE BASED ON STATUS AND ROLE-->
                <?php if((isAllow('remediation','update_evidence_approval_second'))&& ($this->remediation_status == 'EP') && ($row['ev_sso_evaluation'] == 'APPROVED') && ($row['ev_fsa_evaluation'] == 'NONE')){
                ?>
                    <td>
                        <form action='remediation_modify.php' method='POST'>
                            <input type='hidden' name='remediation_id' value='<?php echo $this->remediation_id;?>'>
                            <input type='hidden' name='ev_id'          value='<?php echo $row['ev_id'];?>'>
                            <input type='hidden' name='root_comment'   value='<?php echo $this->root_comment;?>'>
                            <input type='hidden' name='target'         value='evidence'>
                            <input type='hidden' name='action'         value='fsa_evaluate'>
                            <input type='hidden' name='validated'      value='no'>
                            <input type='hidden' name='approved'       value='no'>

                        <b>IV&V Evaluation:</b> 
                    
                            <input type='hidden' name='form_action' value='Evaluate'>
                            <input type='image' src='/images/button_modify.png' name='form_action' value='Evaluate'> 
                            <span><?php echo $row['ev_fsa_evaluation'];?></span>
                        </form>
                    </td>
                <?php } else { ?>
                    <td><b>IV&V Evaluation:</b> <?php echo $row['ev_fsa_evaluation'];?></td>
                         <?php if(isset($row['comments']['EV_FSA']) && $row['comments']['EV_FSA'] != ''){ ?>
                    <td width="85%">
                        <table border="0" cellpadding="3" cellspacing="1" class="tipframe" width="100%">
                            <tr><th align='left'><?php echo '##'.$row['comments.EV_FSA.comment_topic'];?></th></tr>
                            <tr><td ><?php echo $row['comments.EV_FSA.comment_body'];?></td></tr>
                            <tr><td align='right'><i><?php echo $row['comments.EV_FSA.comment_date'];?> by <?php echo $row['comments.EV_FSA.user_name'];?></i></td></tr>
                        </table>
                    </td>
                        <?php } } ?>
                </tr>
                <!-- IVV EVALUATION -->
                <tr>
                    <!-- RESTRICT UPDATE BASED ON STATUS AND ROLE -->
                    <?php if((isAllow('remediation','update_evidence_approval_second'))&& ($this->remediation_status == 'ES') && ($row['ev_sso_evaluation'] == 'APPROVED') && ($row['ev_fsa_evaluation'] == 'APPROVED')){
                    ?>
                        <td>
                            <form action='remediation_modify.php' method='POST'>
                                <input type='hidden' name='remediation_id' value='<?php echo $this->remediation_id;?>'>
                                <input type='hidden' name='ev_id'          value='<?php echo $row['ev_id'];?>'>
                                <input type='hidden' name='root_comment'   value='<?php echo $this->root_comment;?>'>
                                <input type='hidden' name='target'         value='evidence'>
                                <input type='hidden' name='action'         value='ivv_evaluate'>
                                <input type='hidden' name='validated'      value='no'>
                                <input type='hidden' name='approved'       value='no'>
                            <b>Final Evaluation:</b> 
                                <input type='hidden' name='form_action' value='Evaluate'>
                                <input type='image' src='/images/button_modify.png' name='form_action' value='Evaluate'> 
                                <span><?php echo $row['ev_ivv_evaluation'];?></span>
                            </form>
                        </td>
                    <?php } else { ?>
                        <td><b>Final Evaluation:</b><?php echo $row['ev_ivv_evaluation'];?></td>
                        <?php if(isset($row['comments']['EV_IVV']) && $row['comments']['EV_IVV'] != ''){ ?>
                        <td width="85%">
                            <table border="0" cellpadding="3" cellspacing="1" class="tipframe" width="100%">
                                <tr><th align='left'><?php echo $row['comments.EV_IVV.comment_topic'];?></th><tr>
                                <tr><td ><?php echo $row['comments.EV_IVV.comment_body'];?></td></tr>
                                <tr><td align='right'><i><?php echo $row['comments.EV_IVV.comment_date'];?> by <?php echo $row['comments.EV_IVV.user_name'];?></i></td></tr>
                            </table>
                        </td>
                    <?php } } ?>
                </tr>
            </table>
        </td>
    </tr>
        <?php }  } } ?>
        <!-- RESTRICT UPDATE BASED ON STATUS AND ROLE-->
        <?php if(isAllow('remediation','update_evidence')){ ?>
            <tr align='left'>
            <?php if(($this->remediation_status == 'EN') || ($this->remediation_status == 'EO')){ ?>
                <td colspan="2">
                    <form action='remediation_modify.php' method='POST'>
                        <input type='hidden' name='remediation_id' value='<?php echo $this->remediation_id;?>'>
                        <input type='hidden' name='root_comment'   value='<?php echo $this->root_comment;?>'>
                        <input type='hidden' name='target'         value='evidence'>
                        <input type='hidden' name='action'         value='add'>
                        <input type='hidden' name='validated'      value='no'>
                        <input type='hidden' name='approved'       value='no'>
                        <input type='hidden' name='uploaded'       value='no'>
                        <input type='hidden' name='form_action'    value='Submit Evidence'>
                        <input type='button' name="form_action" title='Submit Evidence' value="Upload Evidence">
                    </form>
                </td>
            <?php } else { ?>
                <td colspan="2">
                    <!-- SAVE MODIFICATIONS TO EVIDENCE -->
                    <form action='remediation_modify.php' method='POST'>
                        <input type='hidden' name='action'         value='add'>
                        <input type='hidden' name='validated'      value='no'>
                        <input type='hidden' name='approved'       value='no'>
                        <input type='hidden' name='target'         value='save_poam'>
                        <input type='hidden' name='remediation_id' value='<?php echo $this->remediation_id;?>'>
                        <input type='hidden' name='form_action' value=''>
                        <input type='submit' title='Save or Submit' value="Save" style="cursor: pointer;">
                    </form>
                </td>
            </tr>
          <?php } } ?>
    </table>
    <br>
<?php } } ?>
<!-- ------------------------------------------------------------------------ -->
<!-- Heading Block -->
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="13"><img src="/images/left_circle.gif" border="0"></td>
        <td bgcolor="#DFE5ED"><b>Audit Log</b></td>
        <td bgcolor="#DFE5ED" align="right"></td>
        <td width="13"><img src="/images/right_circle.gif" border="0"></td>
    </tr>
</table>
<!-- End Heading Block -->

    <br>

<!-- COMMENT TABLE -->
<!-- <th align="left">Logs <i>({$num_logs} total)</i></th> -->

    <!-- loop through the logs -->
    <?php if($this->num_logs > 0){ ?>
<table border="0" align="center" cellpadding="5" cellspacing="1" width="95%" class="tbframe">
    <tr>
        <th>Timestamp</td>
        <th>User</td>
        <th>Event</td>
        <th>Description</td>
    </tr>
    <?php foreach($this->logs as $row){ ?>
    <tr>
        <td class="tdc"><?php echo $row['time'];?></td>
        <td class="tdc"><?php echo $row['user_name'];?></td>
        <td class="tdc"><?php echo $row['event'];?></td>
        <td class="tdc"><?php echo $row['description'];?></td>
    </tr>
    <?php } ?>
</table>
<?php }?>
<!-- COMMENT TABLE -->
