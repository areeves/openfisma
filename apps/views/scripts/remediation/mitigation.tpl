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
                        <b>Type:</b>
                        <div id="poam_type" name="poam_type" type="select"
                             option='{"CAP":"(CAP) Corrective Action Plan",                                                                          "AR":"(AR) Accepted the Risk",
                                      "FP":"(FP) Prove False Positive"}'>
                        <span class="sponsor">
                        <?php if(isAllow('remediation','update')){
                            if('OPEN' == $this->remediation['poam_status']){
                        ?>
                        <img src='/images/button_modify.png' style="cursor:pointer;">
                        <? } } ?></span>
                        <span class="contenter"><?php echo $this->remediation['poam_type'];?></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Description:</b>
                        <div id="description" type="textarea" name="poam_action_planned" rows="5" cols="160">
                        <?php if(isAllow('remediation','update_finding_course_of_action')){
                             if('OPEN' == $this->remediation['poam_status']){
                        ?>
                        <span class="sponsor">
                        <img src='/images/button_modify.png' style="cursor:pointer;">
                        </span>
                        <? } }?>
                        <span class="contenter"><?php echo $this->remediation['poam_action_planned'];?></span>
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
                        <div id="resources" type="textarea" name="poam_action_resources" rows="5" cols="160">
                        <?php if(isAllow('remediation','update_finding_resources')){?>
                        <span class="sponsor">
                        <img src='/images/button_modify.png' style="cursor:pointer;">
                        </span>
                        <? } ?>
                       <span class="contenter"><?php echo $this->remediation['poam_action_resources'];?></span>
                    </td>
                </tr>
            </table>
            <!-- End Resources Required for Course of Action Table -->

        </td>
    </tr>

    <tr>
        <td width='50%'>
            <b>Estimated Completion Date:</b>
            <div id="date_est" type="text" name="poam_action_date_est" size="20">
            <?php if(isAllow('remediation','update_est_completion_date')){
                if('OPEN' == $this->remediation['poam_status']){
            ?>
            <span class="sponsor">
            <img src='/images/button_modify.png' style="cursor:pointer;">
            </span>
            <?php } }?>
            <span class="contenter"><?php echo $this->remediation['poam_action_date_est'];?></span>
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

