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
                    <b>Number:</b>
                    <div id="blscr" type="select" name="poam_blscr"
                        option='{<?php foreach($this->all_values as $value){?>
                        "<?php echo $value;?>":"<?php echo $value;?>",
                        <?php } ?> }'>
                        <?php if(isAllow('remediation','update_control_assignment')){ ?>
                            <span class="sponsor">
                            <img src='/images/button_modify.png' style="cursor:pointer;">
                            </span>
                        <?php } ?>
                        <span class="contenter"></span>
                    </div>
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
                        <div id="blscr" type="select" name="poam_blscr"
                             option='{<?php foreach($this->all_values as $value){?>
                            "<?php echo $value;?>":"<?php echo $value;?>",
                            <?php } ?> }'>
                            <?php if(isAllow('remediation','update_control_assignment')){ ?>
                                <span class="sponsor">
                                <img src='/images/button_modify.png' style="cursor:pointer;">
                                </span>
                            <?php } ?>
                            <span class="contenter"><?php echo $this->blscr['blscr_number'];?></span>
                        </div>
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
                <?php if($this->blscr['blscr_enhancements'] == '.'){
                          echo'<i>(none given)</i>';
                      }else {
                          echo $this->blscr['blscr_enhancements'];
                      }
                ?>
            </td></tr>
            <tr><td><b>Supplement: </b>
                <?php if($this->blscr['blscr_supplement'] == '.'){
                          echo'<i>(none given)</i>';
                      }else {
                          echo $this->blscr['blscr_supplement'];
                      }
                ?>
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

