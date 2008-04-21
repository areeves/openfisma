<script LANGUAGE="JavaScript" type="test/javascript" src="/opt/reyo/fismazf/public/javascripts/ajax.js"></script>
<br>

<!-- Heading Block -->
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="13"><img src="/images/left_circle.gif" border="0"></td>
        <td bgcolor="#DFE5ED"><b>Finding Creation</b></td>
        <td bgcolor="#DFE5ED" align="right"><?php echo date('Y-M-D h:i:s:A');?></td>
        <td width="13"><img src="/images/right_circle.gif" border="0"></td>
    </tr>
</table>
<!-- End Heading Block -->

<br>
<?php
    if($this->msg != ''){
?>
<p><b><u><?php echo $this->msg;?></u></b></p>
<?php }?>

<form name="finding" method="post" action="findingdetail.php" onsubmit="return qok();">
<input type="hidden" name="act"           value="<?php echo $this->act;?>">
<input type="hidden" name="do"            value="create">
<input type="hidden" name="vuln_offset"   value="0">
<input type="hidden" name="NUM_VULN_ROWS" value="50">

        <table border="0" align="center" cellpadding="5">
            <tr>
                <td>
                    <input name="button" type="submit" id="button" value="Create Finding" style="cursor:pointer;">
                    <input name="button" type="reset" id="button" value="Reset Form" style="cursor:pointer;">
                </td>
            </tr>
            
            <tr>
                <td>
                    <!-- Begin General Information Table -->
                    <table border="0" width="800" cellpadding="5" class="tipframe">
                        <tr>
                            <th align="left">General Information</th>
                        </tr>
                        <tr>
                            <td>
                                
                                <table border="0" cellpadding="1" cellspacing="1">
                                    <tr>
                                        <td align="right"><b>Discovered Date:</b></td>
                                        <td>
                                            <!-- Begin Date Discovered Table: Date Input and Date Select Image -->
                                            <table border="0" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td><input type="text" name="discovereddate" size="12" maxlength="10" value="<?php echo $this->discovered_date;?>">&nbsp;</td>
                                                    <td><span onclick="javascript:show_calendar('finding.discovereddate');"><img src="/images/picker.gif" width=24 height=22 border=0></span></td>
                                                </tr>
                                            </table>
                                            <!-- End Date Discovered Table: Date Input and Date Select Image -->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right"><b>Finding Source:</b></td>
                                        <td>
                                            <select name="source">
                                            <?php foreach($this->source_list as $sid=>$sname){
                                                      echo'<option value='.$sid.'>'.$sname.'</option>';
                                                  }
                                            ?>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            
                            </td>
                        </tr>
                        <tr>
                            <td><b>Enter Description of Finding:<b><br>
                                <textarea name="finding_data" cols="60" rows="5" style="border:1px solid #44637A; width:100%; height:70px;"></textarea>
                            </td>
                        </tr>
                    </table>
                    <!-- End General Information Table -->
                </td>
            </tr>
            <tr>
                <td>
                    <!-- Asset Information Table -->
                    <table border="0" width="800" cellpadding="5" class="tipframe">
                        <th align="left" colspan="2">Asset Information</th>
                        <tr>
                            <td colspan="2">
                                <!-- System Name and Asset Search Table -->
                                <table border="0" cellpadding="5">
                                    <tr>
                                        <td><b>System:<b></td>
                                        <td>
                                            <select name="system" url="/zfentry.php/asset/search">
                                            <option value="">--Any--</option>
                                            <?php foreach($this->system_list as $sid=>$sname){
                                                     if($this->system == $sid){
                                                        echo'<option value='.$sid.' selected>'.$sname.'</option>';
                                                     }else {
                                                        echo'<option value='.$sid.'>'.$sname.'</option>';
                                                     }
                                                  }
                                            ?>
                                            </select>&nbsp;
                                        </td>
                                        <td><b>Asset Name:<b></td>
                                        <td>
                                            <input type="text" name="asset_name" value="" maxlength="10" size="10">&nbsp;
                                        </td>
                                        <td>
                                            <input name="asset_name" type="button" value="Search Assets" url='/zfentry.php/asset/search/name' style="cursor:pointer;">
                                            <input type="button" value="Create Asset" url='/zfentry.php/asset/create' style="cursor:pointer;">
                                        </td>
                                    </tr>
                                </table>
                                <!-- End System Name and Asset Search Table -->
                            </td>
                        </tr>
                        <tr>
                            <td width="200" align="center">
                                <select id="asset_list" name="asset_list" size="10" style="width: 190px;">
                                <option value="">--None--</option>
                                <?php foreach($this->asset_list as $aid=>$aname){
                                          echo'<option value='.$aid.'>'.$aname.'</option>';
                                      }
                                ?>
                                </select>
                            </td>
                            <td width="600" align="center" valign="top">
                                <fieldset style="height:115; border:1px solid #44637A; padding:5">
                                <legend><b>Asset Information</b></legend>
                                <div id="asset_info"></div>
                                </fieldset>
                            </td>
                        </tr>
                    </table>
                    <!-- Asset Information Table -->
                </td>
            </tr>
        </table>
</form>
