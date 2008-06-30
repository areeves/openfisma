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
                        <span name="poam[type]"
    <?php
        if(('NEW' == $this->poam['status'] || 'OPEN' == $this->poam['status'])&& isAllow('remediation','update')){
            echo ' type="select" class="editable" 
                   href="/zfentry.php/metainfo/list/o/type/format/html/"';
        }
        echo '>',$this->poam['type'];
    ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Description:</b>
                        <span name="poam[action_planned]"
       <?php 
           if('OPEN' == $this->poam['status'] && isAllow('remediation','update_finding_course_of_action')){
               echo ' type="textarea" rows="5" cols="160" class="editable" ';
           }
           echo '>',$this->poam['action_planned'];
       ?>           
                        </span>
                    </td>
                </tr>
            </table>
            <!-- End Course of Action Table -->

            <!-- Resources Required for Course of Action Table -->
            <table width="100%" cellpadding="5" class="tipframe">
                <th align="left">Resources Required for Course of Action</th>
                <tr>
                    <td>
                        <span name="poam[action_resources]" type="textarea" rows="5" cols="160"
    <?php
        if($this->poam['status'] != 'NEW' && isAllow('remediation','update_finding_resources')){
            echo ' class="editable" ';
        }
        echo '>',$this->poam['action_resources'];
    ?>
                        </span>
                    </td>
                </tr>
            </table>
            <!-- End Resources Required for Course of Action Table -->

            <div style="width:95%;margin:0 5px"> 
            <b>Estimated Completion Date:</b>
            <span name="poam[action_est_date]" type="text" <?php
        if($this->poam['status'] == 'OPEN' && 
            isAllow('remediation','update_est_completion_date')){
            echo ' class="date editable" ';
        }
        echo '>',nullGet($this->poam['action_est_date'],'0000-00-00');
    ?></span>
            <b>Actual Completion Date:</b>
            <?php echo nullGet($this->poam['action_actual_date'],'<i>(action not yet completed)</i>');?>
            </div>
