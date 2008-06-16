     <div class="barleft">
     <div class="barright">
     <p><b>Mitigation Strategy</b><span></span></p>
     </div>
     </div>

            <table cellpadding="5" class="tipframe">
                <tr><th align="left">Course of Action</th></tr>
                <tr>
                    <td align="left">
                        <b>Type:</b>
                        <div id="poam_type" name="type" type="select"
                             option='{"CAP":"(CAP) Corrective Action Plan",                                                                          "AR":"(AR) Accepted the Risk",
                                      "FP":"(FP) Prove False Positive"}'>
                        <span class="sponsor">
                        <?php if(isAllow('remediation','update')){
                            if('OPEN' == $this->poam['status']){
                        ?>
                        <img src='/images/button_modify.png' style="cursor:pointer;">
                        <? } } ?></span>
                        <span class="contenter"><?php echo $this->poam['type'];?></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Description:</b>
                        <div id="description" type="textarea" name="action_planned" rows="5" cols="160">
                        <?php if(isAllow('remediation','update_finding_course_of_action')){
                             if('OPEN' == $this->poam['status']){
                        ?>
                        <span class="sponsor">
                        <img src='/images/button_modify.png' style="cursor:pointer;">
                        </span>
                        <? } }?>
                        <span class="contenter"><?php echo $this->poam['action_planned'];?></span>
                    </td>
                </tr>
            </table>
            <!-- End Course of Action Table -->

            <!-- Resources Required for Course of Action Table -->
            <table width="100%" cellpadding="5" class="tipframe">
                <th align="left">Resources Required for Course of Action</th>
                <tr>
                    <td>
                        <div id="resources" type="textarea" name="action_resources" rows="5" cols="160">
                        <?php if(isAllow('remediation','update_finding_resources')){?>
                        <span class="sponsor">
                        <img src='/images/button_modify.png' style="cursor:pointer;">
                        </span>
                        <? } ?>
                       <span class="contenter"><?php echo $this->poam['action_resources'];?></span>
                    </td>
                </tr>
            </table>
            <!-- End Resources Required for Course of Action Table -->

            <div style="width:95%;margin:0 5px"> 
            <b>Estimated Completion Date:</b>
            <span class="contenter"><?php echo $this->poam['action_est_date'];?></span>
            <?php if(isAllow('remediation','update_est_completion_date')){
                if('OPEN' == $this->poam['status']){
                    //be able to modify
                } 
            }?>

            <b>Actual Completion Date:</b>
            <?php echo nullGet($this->poam['action_actual_date'],'<i>(action not yet completed)</i>');?>
            </div>
