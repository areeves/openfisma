<script LANGUAGE="JavaScript" type="test/javascript" src="/javascripts/ajax.js"></script>
<div class="barleft">
<div class="barright">
<p><b>Finding Creation</b><span><?PHP echo $this->escape($this->Current_time);?></span></p>
</div>
</div>
<br>

<form name="finding" method="post" action="/zfentry.php/finding/create/is/new" >
<table width="810" border="0" align="center" cellpadding="5">
    <tr><td>
        <input name="button" type="submit" id="button" value="Create Finding" >
        <input name="button" type="reset" id="button" value="Reset Form" >
    </td></tr>
    <tr><td>
        <table border="0" width="800" cellpadding="5" class="tipframe">
            <tr> <th align="left">General Information</th> </tr>
            <tr> <td>
                    <table border="0" cellpadding="1" cellspacing="1">
                        <tr>
                            <td align="right"><b>Discovered Date:</b></td>
                            <td>
                                <table border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td><input type="text" name="discovereddate" size="12" maxlength="10" value="<?php echo $this->discovered_date;?>">&nbsp;</td>
                                        <td><span onclick="javascript:show_calendar('finding.discovereddate');"><img src="/images/picker.gif" width=24 height=22 border=0></span></td>
                                    </tr>
                                </table>
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
            </td>
            </tr>
            <tr>
                <td>
                    <table border="0" width="800" cellpadding="5" class="tipframe">
                        <tr><th align="left">Asset Information
                        </th>
                        <th align="right"><a href="/zfentry.php/asset/create">Create Asset</a>
                        </th>
                        <tr>
                            <td colspan="2">
                                <table width="100%" border="0" cellpadding="5">
                                    <tr>
                                        <td><b>System:</b></td>
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
                                            </select>&nbsp;                                        </td>
                                        <td><b>Asset Name:</b></td>
                                        <td><input class='assets' type="text" name="name" value="<?php echo $this->param['port']; ?>" size="10" />                                          &nbsp;                                        </td>
                                      </tr>
                                    <tr>
                                    				<td><b>IP Address:</b></td>
                                    				<td><input class='assets' type="text" name="ip" value="<?php echo $this->param['ip']; ?>" maxlength="23" /></td>
                                    				<td><b>Port:<b></b></b></td>
                                    				<td><input class='assets' type="text" name="port" value="<?php echo $this->param['port']; ?>" size="10" /></td>
                                    				</tr>
                                    <tr>
                                      <td>&nbsp;</td>
                                      <td><input id="search_asset" type="button" value="Search Assets" url='/zfentry.php/asset/search' /></td>
                                      <td colspan=2 ><input type="reset" name="button2" id="button2" value="Reset" /></td>
                                      </tr>
                                </table><hr/>
                            </td>
                        </tr>
                        <tr>
                            <td width="200" align="center"><b>Asset Name:</b><div>
                                <select id="asset_list" name="asset_list" size="8" style="width: 190px;">
                                <?php foreach($this->asset_list as $aid=>$aname){
                                          echo'<option value='.$aid.'>'.$aname.'</option>';
                                      }
                                ?>
                                </select></div>                            </td>
                            <td width="600" align="center" valign="top">
                                <fieldset style="height:115; border:1px solid #44637A; padding:5">
                                <legend><b>Asset Information</b></legend>
                                <div id="asset_info"></div>
                                </fieldset>
						    </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
</form>
