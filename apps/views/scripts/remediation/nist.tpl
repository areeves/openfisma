<div class="barleft">
<div class="barright">
<p><b>NIST 800-53 Control Mapping</b><span></span></p>
</div>
</div>
        <!-- BLSCR TABLE -->
        <table border="0" width="95%" align="center" cellpadding="5" class="tipframe">
            <tr>
                <th align="left" >Security Control
                <span id="blscr" type="select" name="blscr_id"
                    <?php if(isAllow('remediation','update_control_assignment')){ ?>
                        <img src='/images/button_modify.png' style="cursor:pointer;">
                    <?php } ?>
                </span>
                </th>
            </tr>
            <?php  if( !empty($this->poam['blscr']) ) { 
                $blscr = $this->poam['blscr']; ?>
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
                    <td class="tdc"><?php echo $blscr['code'];?></td>
                    <td class="tdc"><?php echo $blscr['class'];?></td>
                    <td class="tdc"><?php echo $blscr['family'];?></td>
                    <td class="tdc"><?php echo $blscr['subclass'];?></td>
                    <td class="tdc" align="center">
                        <?php echo 'low' == $blscr['control_level']?'Control Required':'Control Not Required';?>
                    </td>
                    <td class="tdc" align="center">
                        <?php echo 'moderate' == $blscr['control_level']?'Control Required':'Control Not Required';?>
                    </td>
                    <td class="tdc" align="center">
                        <?php echo 'high' == $blscr['control_level']?'Control Required':'Control Not Required';?>
                    </td>
                </tr>
                </table>
                </td>
            </tr>
            <tr><td><b>Control: </b> <?php echo $blscr['control'];?></td></tr>
            <tr><td><b>Guidance: </b> <?php echo $blscr['guidance'];?></td></tr>
            <tr><td><b>Enhancements: </b>
                <?php echo nullGet($blscr['enhancements'],'<i>(none given)</i>'); ?>
            </td></tr>
            <tr><td><b>Supplement: </b>
                <?php echo nullGet($blscr['supplement'],'<i>(none given)</i>'); ?>
            </td></tr>
            <?php  }  ?>
        </table>
<!-- Heading Block -->
<div class="barleft">
<div class="barright">
<p><b>Risk Analysis</b><span></span></p>
</div>
</div>
            <table cellpadding="5" cellspacing="1" class="tipframe">
                <tr><th align="left" >Risk Analysis Form</th></tr>
                <tr>
                    <td>
                        Based on the guidance provided by NIST Special Publication 800-37, to derive an overall likelihood rating that indicates the probability that a potential vulnerability may be exercised, we must first define the threat-source motivation and capability while considering the nature of the vulnerability and the existence and effectiveness of current controls or countermeasures. The following two sections on Threat Information and Countermeasure Information will help us define the iformation required to generate a threat likelihood risk level which will be used to generate the overall risk level of this vulnerability as it pertains to your information system.
                    </td>
                </tr>
                <tr>
                        <!--RESTRICT BY ROLE -->
                        <?php if(isAllow('remediation','generate_raf')){ ?>
                        <!-- CHECK THAT CMEASURE AND THREAT LEVEL ARE SET-->
                        <?php if(($this->poam['threat_level'] != 'NONE') && 
                                 ($this->poam['cmeasure_effectiveness'] != 'NONE')){ ?>
                        <!--<form action='raf.php' method='POST' target='_blank'>
                        <input type='hidden' name='poam_id'     value='<?php echo $this->poam_id;?>'>
                    <td colspan='2'>
                        <input type='hidden' name='form_action' value='Generate RAF'>
                        <input type='submit' name='form_action' value='Generate RAF'>
                    </td>
                        </form>-->
                        <?php } else { ?>
                    <td><i>(Threat and Countermeasure information must be completed to generate a RAF)</i></td>
                        <?php } ?>
                        <?php } else { ?>
                    <td colspan='2'>&nbsp;</td>
                        <?php } ?>
                </tr>
            </table>

            <!-- THREATS TABLE -->
            <table cellpadding="5" cellspacing="1" class="tipframe" >
                <tr><th align='left'>Threat Information</th></tr>
                <tr>
                    <td> A threat is the potential for a particular threat-source to successfully exercise a particular vulnerability. A vulnerability is a weakness that can be accidentally triggered or intentionally exploited. A threat-source does not present a risk when there is no vulnerability that can be exercised. In determining the likelihood of a threat, one must consider threat-sources, potential vulnerabilities, and existing controls. Common threat sources are: (1) Natural Threats: Floods, earthquakes, tornadoes, landslides, avalanches, electrical storms, and other such events, (2) Human Threats: Events that are either enabled by or caused by human beings, such as unintentional acts (inadvertent data entry) or deliberate actions (network based attacks, malicious software upload, unauthorized access to confidential information), and (3) Environmental Threats: Long-term power failure, pollution, chemicals, liquid leakage.
                        </td>
                </tr>
                <tr>
                    <td>
                    <b>Level:</b>
                    <div id ="threat" type="select" name="threat_level" 
                                 option='{"NONE":"NONE","LOW":"LOW","MODERATE":"MODERATE","HIGH":"HIGH"}'>
                            <?php if(isAllow('remediation','update_threat')){ ?>
                            <span class="sponsor">
                            <img src='/images/button_modify.png' style="cursor:pointer;">
                            </span>
                            <?php } ?>
                            <span class="contenter"><?php echo $this->poam['threat_level'];?></span>
                            </div>
                    </td>
                 </tr>
                 <tr>
                     <td>
                     <b>Source:</b>
                     <div id ="source" type="textarea" name="threat_source" rows="5" cols="160">
                         <?php if(isAllow('remediation','update_threat')){ ?>
                            <span class="sponsor">
                            <img src='/images/button_modify.png' style="cursor:pointer;">
                            </span>
                            <?php } ?>
                            <span class="contenter">
                                <?php echo $this->poam['threat_source'];?>
                            </span>
                     </div>
                     </td>
                 </tr>
                 <tr>
                     <td>
                            <b>Justification:</b>
                            <div id ="justification" type="textarea" name="threat_justification"
                                 rows="5" cols="160">
                            <?php if(isAllow('remediation','update_threat')){ ?>
                            <span class="sponsor">
                            <img src='/images/button_modify.png' style="cursor:pointer;">
                            </span>
                            <?php } ?>
                            <span class="contenter">
                                <?php echo $this->poam['threat_justification'];?>
                            </span>
                            </div>
                        </td>
                    </tr>
                </table>
                 <!-- END THREATS TABLE -->
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
                            <div id="effectivness" type="select" name="cmeasure_effectiveness"
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
                            <div id="cmeasure" type="textarea" name="cmeasure" rows="5" cols="160">
                            <!--RESTRICT UPDATE BASED ON STATUS AND ROLE-->
                            <?php if(isAllow('remediation','update_cmeasure')){ ?>
                            <span class="sponsor">
                            <img src='/images/button_modify.png' style="cursor:pointer;">
                            </span>
                            <?php } ?>
                            <span class="contenter"><?php echo $this->poam['cmeasure'];?></span>
                            </div>
                        </td>
                    </tr>
                     <tr>
                        <td>
                            <b>Justification:</b>
                            <div id="cmeasure_justification" type="textarea" name="cmeasure_justification"
                                rows="5" cols="160">
                            <!--RESTRICT UPDATE BASED ON STATUS AND ROLE-->
                            <?php if(isAllow('remediation','udpate_cmeasure')){ ?>
                            <span class="sponsor">
                            <img src='/images/button_modify.png' style="cursor:pointer;">
                            </span>
                            <?php } ?>
                            <span class="contenter">
                                <?php echo $this->poam['cmeasure_justification'];?>
                            </span>
                            </div>
                        </td>
                    </tr>
                </table>
            <!-- END COUNTERMEASURE TABLE -->

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
            <div id="sso_approval" type="select" name="action_status"
                option='{"APPROVED":"APPROVED","DENIED":"DENIED"}'>                    
            <!--RESTRICT UPDATE BASED ON STATUS AND ROLE-->
            <?php if(isAllow('remediation','update_mitigation_strategy_approval')){
                if($this->is_completed == 'yes'){
                    if(($this->poam['status'] == 'OPEN') || ($this->poam['status'] == 'EN') || 
                         ($this->poam['status'] == 'EO')){ ?>
                <span class="sponsor">
                <img src='/images/button_modify.png' style="cursor:pointer;">
                </span>
            <?php }  }  } ?>
            <span class="contenter"><?php echo $this->poam['action_status'];?></span>
            </div>
        </td>
    </tr>
</table>

