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
                        <!--<form action='raf.php' method='POST' target='_blank'>
                        <input type='hidden' name='poam_id'     value='<?php echo $this->remediation_id;?>'>
                    <td colspan='2'>
                        <input type='hidden' name='form_action' value='Generate RAF'>
                        <input type='submit' name='form_action' value='Generate RAF'>
                    </td>
                        </form>-->
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
                            <b>Level:</b>
                            <div id ="threat" type="select" name="poam_threat_level" 
                                 option='{"NONE":"NONE","LOW":"LOW","MODERATE":"MODERATE","HIGH":"HIGH"}'>
                            <?php if(isAllow('remediation','update_threat')){ ?>
                            <span class="sponsor">
                            <img src='/images/button_modify.png' style="cursor:pointer;">
                            </span>
                            <?php } ?>
                            <span class="contenter"><?php echo $this->threat_level;?></span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>Source:</b>
                            <div id ="source" type="textarea" name="poam_threat_source" rows="5" cols="160">
                            <?php if(isAllow('remediation','update_threat')){ ?>
                            <span class="sponsor">
                            <img src='/images/button_modify.png' style="cursor:pointer;">
                            </span>
                            <?php } ?>
                            <span class="contenter">
                                <?php echo $this->remediation['poam_threat_source'];?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>Justification:</b>
                            <div id ="justification" type="textarea" name="poam_threat_justification"
                                 rows="5" cols="160">
                            <?php if(isAllow('remediation','update_threat')){ ?>
                            <span class="sponsor">
                            <img src='/images/button_modify.png' style="cursor:pointer;">
                            </span>
                            <?php } ?>
                            <span class="contenter">
                                <?php echo $this->remediation['poam_threat_justification'];?>
                            </span>
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
                            <b>Effectiveness:</b>
                            <div id="effectivness" type="select" name="poam_cmeasure_effectiveness"
                                option='{"NONE":"NONE","LOW":"LOW","MODERATE":"MODERATE","HIGH":"HIGH"}'>
                            <!-- RESTRICT UPDATE BASED ON STATUS AND ROLE-->
                            <?php if(isAllow('remediation','update_cmeasures')){ ?>
                            <span class="sponsor">
                            <img src='/images/button_modify.png' style="cursor:pointer;">
                            </span>
                            <?php } ?>
                            <span class="contenter">
                                <?php echo $this->cmeasure_effectiveness;?>
                            </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>Countermeasure:</b>
                            <div id="cmeasure" type="textarea" name="poam_cmeasure" rows="5" cols="160">
                            <!--RESTRICT UPDATE BASED ON STATUS AND ROLE-->
                            <?php if(isAllow('remediation','update_cmeasure')){ ?>
                            <span class="sponsor">
                            <img src='/images/button_modify.png' style="cursor:pointer;">
                            </span>
                            <?php } ?>
                            <span class="contenter"><?php echo $this->remediation['poam_cmeasure'];?></span>
                        </td>
                    </tr>
                     <tr>
                        <td>
                            <b>Justification:</b>
                            <div id="cmeasure_justification" type="textarea" name="poam_cmeasure_justification"
                                rows="5" cols="160">
                            <!--RESTRICT UPDATE BASED ON STATUS AND ROLE-->
                            <?php if(isAllow('remediation','udpate_cmeasure')){ ?>
                            <span class="sponsor">
                            <img src='/images/button_modify.png' style="cursor:pointer;">
                            </span>
                            <?php } ?>
                            <span class="contenter">
                                <?php echo $this->remediation['poam_cmeasure_justification'];?>
                            </span>
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
                    <b>SSO Approval:</b><!-- Action Approval-->
                    <div id="sso_approval" type="select" name="poam_action_status"
                        option='{"APPROVED":"APPROVED","DENIED":"DENIED"}'>                    
                    <!--RESTRICT UPDATE BASED ON STATUS AND ROLE-->
                    <?php if(isAllow('remediation','update_mitigation_strategy_approval')){
                        if(($this->remediation_type != 'NONE') && ($this->is_completed == 'yes')){
                            if(($this->remediation_status == 'OPEN') || ($this->remediation_status == 'EN') || ($this->remediation_status == 'EO')){ ?>
                        <span class="sponsor">
                        <img src='/images/button_modify.png' style="cursor:pointer;">
                        </span>
                    <?php }  }  } ?>
                    <span class="contenter"><?php echo $this->remediation['poam_action_status'];?></span>
                    </div>
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
            <form action="/zfentry.php/panel/remediation/sub/modify/id/<?php echo $this->remediation_id;?>" method="post">
            <input type='hidden' name='poam_action_owner' value=''>
            <input type='hidden' name='poam_action_suggested' value=''>
            <input type='hidden' name='poam_type' value=''>
            <input type='hidden' name='poam_action_planned' value=''>
            <input type='hidden' name='poam_action_resources' value=''>
            <input type='hidden' name='poam_action_date_est' value=''>
            <input type='hidden' name='poam_blscr' value=''>
            <input type='hidden' name='poam_threat_level' value=''>
            <input type='hidden' name='poam_threat_source' value=''>
            <input type='hidden' name='poam_threat_justification' value=''>
            <input type='hidden' name='poam_cmeasure_effectiveness' value=''>
            <input type='hidden' name='poam_cmeasure' value=''>
            <input type='hidden' name='poam_cmeasure_justification' value=''>
            <input type='hidden' name='poam_action_status' value=''>
            <input type='hidden' name='action_approval' value=''>
            <input type='submit' title='Save or Submit' value="Save" style="cursor: pointer;">
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
<?php if(($this->remediation_status != 'EN') && ($this->remediation_status != 'EO')){ ?>
<form action="/zfentry.php/panel/remediation/sub/modify/id/<?php echo $this->remediation_id;?>" method="post">
<?php } ?>
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
                        <b>ISSO Evaluation:</b>
                            <div id="sso_evaluate" type="select" name="ev_sso_evaluation" 
                                option='{"NONE":"NONE","APPROVED":"APPROVED","DENIED":"DENIED"}'>
                                <span class="sponsor">
                                <img src='/images/button_modify.png' style="cursor:pointer;">
                                </span>
                                <span class="contenter"><?php echo $row['ev_sso_evaluation'];?></span>
                            </div>
                    </td>
                <?php } else { ?>
                    <td><b>ISSO Evaluation:</b><?php echo $row['ev_sso_evaluation'];?></td>
                    <?php if(isset($row['comments']['EV_SSO']) && $row['comments']['EV_SSO'] != ''){ ?>
                    <td width="85%">
                        <table border="0" cellpadding="3" cellspacing="1" class="tipframe" width="100%">
                            <tr><th align='left'><?php echo $row['comments']['EV_SSO']['comment_topic'].$row['comments']['EV_SSO']['comment_log'];?></th></tr>
                            <tr><td ><?php echo $row['comments']['EV_SSO']['comment_body'];?></td></tr>
                            <tr><td align='right'><i><?php echo $row['comments']['EV_SSO']['comment_date'];?>by<?php echo $row['comments']['EV_SSO']['user_name'];?></i></td></tr>
                        </table>
                    </td>
                    <?php } } ?>
                </tr>
                <!--FSA EVALUATION-->
                <tr>
                <!--RESTRICT UPDATE BASED ON STATUS AND ROLE-->
                <?php if((isAllow('remediation','update_evidence_approval_second'))&& ($this->remediation_status == 'EP') && ($row['ev_sso_evaluation'] == 'APPROVED') && ($row['ev_fsa_evaluation'] == 'NONE')){ ?>
                    <td>
                        <b>IV&V Evaluation:</b>
                            <div id="fsa_evaluate" type="select" name="ev_fsa_evaluation" 
                                option='{"NONE":"NONE","APPROVED":"APPROVED","DENIED":"DENIED"}'>
                                <span class="sponsor">
                                <img src='/images/button_modify.png' style="cursor:pointer;">
                                </span>
                                <span class="contenter"><?php echo $row['ev_fsa_evaluation'];?></span>
                            </div>
                    </td>
                <?php } else { ?>
                    <td><b>IV&V Evaluation:</b> <?php echo $row['ev_fsa_evaluation'];?></td>
                         <?php if(isset($row['comments']['EV_FSA']) && $row['comments']['EV_FSA'] != ''){ ?>
                    <td width="85%">
                        <table border="0" cellpadding="3" cellspacing="1" class="tipframe" width="100%">
                            <tr><th align='left'><?php echo $row['comments']['EV_FSA']['comment_topic'];?></th></tr>
                            <tr><td ><?php echo $row['comments']['EV_FSA']['comment_body'];?></td></tr>
                            <tr><td align='right'><i><?php echo $row['comments']['EV_FSA']['comment_date'];?> by <?php echo $row['comments']['EV_FSA']['user_name'];?></i></td></tr>
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
                            <b>Final Evaluation:</b>
                            <div id="ivv_evaluate" type="select" name="ev_ivv_evaluation" 
                                    option='{"NONE":"NONE","APPROVED":"APPROVED","DENIED":"DENIED"}'>
                                <span class="sponsor">
                                <img src='/images/button_modify.png' style="cursor:pointer;">
                                </span>
                                <span class="contenter"><?php echo $row['ev_ivv_evaluation'];?></span>
                            </div>
                        </td>
                    <?php } else { ?>
                        <td><b>Final Evaluation:</b><?php echo $row['ev_ivv_evaluation'];?></td>
                        <?php if(isset($row['comments']['EV_IVV']) && $row['comments']['EV_IVV'] != ''){ ?>
                        <td width="85%">
                            <table border="0" cellpadding="3" cellspacing="1" class="tipframe" width="100%">
                                <tr><th align='left'><?php echo $row['comments']['EV_IVV']['comment_topic'];?></th><tr>
                                <tr><td ><?php echo $row['comments']['EV_IVV']['comment_body'];?></td></tr>
                                <tr><td align='right'><i><?php echo $row['comments']['EV_IVV']['comment_date'];?> by <?php echo $row['comments']['EV_IVV']['user_name'];?></i></td></tr>
                            </table>
                        </td>
                    <?php } } ?>
                </tr>
                <!-- denied input-->
                <tr><td><textarea name="comment_body" rows="2" cols="50">denied info:</textarea></td></tr>
            </table>
        </td>
    </tr>
    <input type='hidden' name='ev_id' value='<?php echo $row['ev_id'];?>'>
        <?php }  } } ?>
         <!-- RESTRICT UPDATE BASED ON STATUS AND ROLE-->
        <?php if(isAllow('remediation','update_evidence')){ ?>
            <tr align='left'>
            <?php if(($this->remediation_status == 'EN') || ($this->remediation_status == 'EO')){ ?>
                <td colspan="2">
                    <button id="up_evidence" onclick ="upload_evidence();">Upload Evidence</button>
                </td>
            <?php } else { ?>
                <td colspan="2">
                    <!-- SAVE MODIFICATIONS TO EVIDENCE -->
                        <input type='hidden' name='ev_sso_evaluation' value=''>
                        <input type='hidden' name='ev_fsa_evaluation' value=''>
                        <input type='hidden' name='ev_ivv_evaluation' value=''>
                        <input type='submit' title='Save or Submit' value="Save" style="cursor: pointer;">
                    </form>
                </td>
            </tr>
          <?php } } ?>
    </table>
    <br>
<?php } } ?>
<div id="maskDiv" style="display: none;"></div>
<div id="editorDIV" style="display: none;">
    <div id="editorTopDIV"><span class="left">Upload Evidence</span><span class="right">close</span></div>
    <div id="editorItem">
    <form enctype="multipart/form-data" method="POST"
         action="/zfentry.php/panel/remediation/sub/upload_evidence/id/<?php echo $this->remediation_id;?>">
         <b>Select File :</b> <input type='file' name='evidence' size='40' value=''>
         <input type="submit" value="Upload Evidence">
    </form><br>
    <ul>
        <li>Please submit <b>all evidence</b> for the finding in a <b>single package</b> (eg, zip file)</li>
        <li>Evidence submissions must be <b>under 10 megabytes</b> in size</li>
        <li>Please ensure no <b>Personally Identifiable Information</b> is included (eg, SSN, DOB)</li>
    </ul>
</div>
</div>
