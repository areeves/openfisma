<?php
    require_once( 'local/rafutil.php');

    $type_name = array('NONE'=>'None',
                       'CAP'=>'Corrective Action Plan',
                       'AR' =>'Accepted Risk',
                       'FP' =>'False Positive');
    $status_name = array('NEW' => 'New',
                         'OPEN' =>'Open',
                         'EN' => 'Evidence Needed',
                         'EO' => 'Evidence Overdue',
                         'EP(SSO)' =>'Evidence Provided to SSO',
                         'EP(S&P)' => 'Evidence Provided to S&P',
                         'ES' => 'Evidence Submitted',
                         'CLOSED' => 'Closed' );

    $cellidx_lookup['HIGH']['LOW']          = 0;
    $cellidx_lookup['HIGH']['MODERATE']     = 1;
    $cellidx_lookup['HIGH']['HIGH']         = 2;
    $cellidx_lookup['MODERATE']['LOW']      = 3;
    $cellidx_lookup['MODERATE']['MODERATE'] = 4;
    $cellidx_lookup['MODERATE']['HIGH']     = 5;
    $cellidx_lookup['LOW']['LOW']           = 6;
    $cellidx_lookup['LOW']['MODERATE']      = 7;
    $cellidx_lookup['LOW']['HIGH']          = 8;

    $sys = new System();
    $rows = $sys->find($this->poam['system_id']);
    $act_owner = $rows->current()->toArray();

    $organization = new Organization();
    $ret = $organization->find($act_owner['organization_id']);
    $organization = $ret->current()->toArray();
?>
<html>
<head>
    
<title>Risk Analysis Form (RAF)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style>
p.pageHeader {
  font-size: 20pt;
  font-family: sans-serif; 
  text-align: center;
  margin: auto auto;
}

div.raf {
  text-align: center;
  margin: auto;
  width: 900px;
}

div.rafHeader {
  padding: 2px;
  border: 2px solid black;
  width: 100%;
  text-align: left;
  font-family: sans-serif;
  font-size: 10pt;
  margin-top: 20px;
  margin-bottom: 20px;
}

table.rafContent {
  width: 100%;
}

table.rafContent td {
  vertical-align: top;
  font-family: sans-serif;
  font-size: 10pt;
}

table.rafImpact {
  margin: auto auto;
  border: none;
  border-collapse: collapse;
  width: 100%;
  color: black;
}

table.rafImpact td {
  padding: 2px;
  border: 2px solid black;
}

</style>
</head>
<body>

<p style="text-align:right"><button onclick="javascript:window.print();">Print</button> </p>

<div class="raf">
<table class="rafContent">
    <tr>
        <td colspan="4"><p class="pageHeader">Risk Analysis Form (RAF)</p></td>
    </tr>
    <tr>
        <td colspan="4"><b>Finding Information</b></td>
    </tr>
    <tr>
        <td width="25%">Finding Number:</td><td width="25%"><?php echo $this->poam['id'];?></td>
        <td width="25%">Finding Source:</td><td width="25%"><?php echo $this->poam['source_name'];?></td>
    </tr>
    <tr>
        <td>Date Opened:</td><td ><?php echo $this->poam['create_ts'];?></td>
        <td>Date Discovered:</td><td ><?php echo $this->poam['discover_ts'];?></td>
    </tr>
    <tr>
        <td >Organization:</td><td><?php echo $organization['name'];?></td>
        <td >Information System:</td><td><?php echo $this->system_list[$this->poam['system_id']];?></td>
    </tr>
</table>

<table class="rafContent">
    <tr>
        <td width="25%">Asset Affected:</td><td width="75%"><?php echo $this->poam['asset_name'];?></td>
    </tr>
    <tr>
        <td >Finding Description:</td><td colspan="2"><?php echo $this->poam['finding_data'];?></td>
    </tr>
    <tr>
        <td >Recommendation:</td><td colspan="2"><?php echo $this->poam['action_suggested'];?></td>
    </tr>
    <tr>
        <td >Risk Level:</td><td colspan="2"><?php echo $this->poam['threat_level'];?></td>
    </tr>

    <tr>
        <td colspan="2"><b>Mitigation Strategy</b></td>
    </tr>
    <tr>
        <td >Course of Action:</td><td ><?php echo $this->poam['action_planned'];?></td>
    </tr>
    <tr>
        <td >Course of Description:</td><td><?php echo $this->poam['action_resources'];?></td>
    </tr>
    <tr>
        <td >Estimated Completion Date:</td><td><?php echo $this->poam['action_current_date'];?></td>
    </tr>
</table>

<table class="rafContent">
    <tr>
        <td colspan="2"><b>Risk Analysis</b></td>
    </tr>
    <tr>
        <td colspan="2" align="center"><b><i>Security Categorization</i></b></td>
    </tr>
    <tr>
        <td width="40%">Security Categorization:</td><td width="60%"><?php echo $act_owner['security_categorization'];?></td>
    </tr>
    <tr>
        <td>Security Categorization Description:</td><td>The loss of confidentiality, integrity, or availability could be expected to have a serious adverse effect on organizational operations, organizational assets, or individuals. This is the maximum level of risk exposure based on the Information System Security Categorization data.</td>
    </tr>
    <tr>
        <td colspan="2" align="center"><b><i>Overall likelihood Rating</i></b></td>
    </tr>
    <tr>
        <td colspan="2">To derive an overall likelihood rating that indicates the probability that a potential vulnerability may be exercised within the construct of the associated threat environment, the following governing factors must be considered: Threat-source motivation and capability, Nature of the vulnerability, and Existence and effectiveness of current controls.</td>
    </tr>
    <tr>
        <td>Threat:</td><td><?php echo $this->poam['threat_source'];?></td>
    </tr>
    <tr>
        <td>Threat Level:</td><td><?php echo $this->poam['threat_level'];?></td>
    </tr></p>
    <tr>
        <td>Countermeasures:</td><td><?php echo $this->poam['cmeasure'];?></td>
    </tr>
    <tr>
        <td>Countermeasures Effectiveness:</td><td><?php echo $this->poam['cmeasure_effectiveness'];?></td>
    </tr>
    <?php
        $sensitivity = calSensitivity(array($act_owner['confidentiality'],
                                            $act_owner['availability'],
                                            $act_owner['integrity']));

        $availability = &$act_owner['availability'];

        $impact = calcImpact($sensitivity, $availability);

        $threat_likelihood = calcThreat($this->poam['threat_level'], 
                                         $this->poam['cmeasure_effectiveness']);
        echo $this->partial('remediation/raf_tl.tpl', array('act_owner'=>$act_owner,
                                                            'poam'=>&$this->poam,
                                                            'table_lookup'=>&$cellidx_lookup,
                                                            'threat_likelihood'=>&$threat_likelihood));
    ?>
    <tr>
       <td colspan="2" align="center"><b><i>Overall Risk Level</i></b></td>
    </tr>
    <?php

        echo $this->partial('remediation/raf_risk.tpl', array('act_owner'=>$act_owner,
                                                              'impact'=>&$impact,
                                                              'table_lookup'=>&$cellidx_lookup,
                                                              'threat_likelihood'=>&$threat_likelihood));
    ?>
    <tr><td colspan="2">Agency Recommendations based on Risk Levels:</td></tr>
    <tr><td colspan="2">
        <table class="rafImpact">
            <tr>
                <td width="20%">Risk Level</td>
                <td>Risk Description and Necessary Actions</td>
            </tr>
            <tr>
                <td>High</td>
                <td>If an observation or finding is evaluated as a high risk, there is a strong need for corrective measures. An existing system may continue to operate, but a corrective action plan must be put in place as soon as possible.</td>
            </tr>
            <tr>
                <td>Moderate</td>
                <td>If an observation is rated as medium risk, corrective actions are needed and a plan must be developed to incorporate these actions within a reasonable period of time.</td>
            </tr>
            <tr>
                <td>Low</td>
                <td>If an observation is described as low risk, the system’s AO must determine whether corrective actions are still required or decide to accept the risk.</td>
            </tr>
        </table>
    </td></tr>
    <tr>
        <td colspan="2"><br>WARNING: This report is for internal, official use only.  This report contains sensitive computer security related information. Public disclosure of this information would risk circumvention of the law. Recipients of this report must not, under any circumstances, show or release its contents for purposes other than official action. This report must be safeguarded to prevent improper disclosure. Staff reviewing this document must hold a minimum of Public Trust Level 5C clearance.
        </td>
  </tr>
</table>
</div>

</body>
</html>
