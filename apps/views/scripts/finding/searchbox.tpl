<div class="barleft">
<div class="barright">
<p><b>Search Findings</b></p>
</div>
</div>
<br>

<form name="finding" method="post" action="/zfentry.php/panel/finding/s/search/">
<table align="center" border="0" cellpadding="5" cellspacing="1" class="tipframe">
    <tr >
        <td id="cell 1" align="right">System:</td>
        <td id="cell 2" align="left">   
        <?php echo $this->formSelect('system', $this->criteria['system'], 
                                        null, $this->system); ?> 
        </td>
        <td id="cell 3" align="right">Source:</td>
        <td id="cell 4" align="left">
        <?php echo $this->formSelect('source', $this->criteria['source'], 
                                        null, $this->source); ?> 
        </td>
    </tr>
    <tr >
        <td align="right">Network:</td>
        <td align="left">
        <?php echo $this->formSelect('network', $this->criteria['network'], 
                                        null, $this->network); ?> 
        </td>
        <td align="right">Status:</td>
        <td align="left">
        <?php echo $this->formSelect('status', $this->criteria['status'], 
                                        null, $this->status); ?> 
        </td>
    </tr>
    <tr >
        <td align="right">IP Address:</td>
        <td align="left"> 
        <input type="text" name="ip" value="<?php echo $this->criteria['ip'];?>" maxlength="20" maxlength="20"></td>
        <td align="right">Port:</td>
        <td align="left"> 
        <input type="text" name="port" value="<?php echo $this->criteria['port']; ?>" size="6" maxlength="6"></td>
    </tr>
    <tr >
        <td id="cell 1" align="right">Vulnerability:</td>
        <td id="cell 2" align="left"><input type="text" name="vuln" value="<?php echo $this->criteria['vuln'];?>" maxlength="20"></td>
        <td id="cell 3" align="right">Product:</td>
        <td id="cell 4" align="left"><input type="text" name="product" value="<?php echo $this->criteria['product']; ?>" maxlength="20"></td>
    </tr>
    <tr >
        <td id="cell 1" align="right">Date Discovered From: </td>
        <td id="cell 2" align="left"><input type="text" name="from" size="12" maxlength="10" value="<?php echo $this->criteria['from']; ?>">             
            <span onclick="javascript:show_calendar('finding.startdate');">
            <img src="/images/picker.gif" width=24 height=22 border=0></span>
        </td>
        <td id="cell 3" align="right">To: </td>
        <td id="cell 4" align="left"><input type="text" name="to" size="12" maxlength="10" value="<?php echo $this->criteria['to']; ?>">
            <span onclick="javascript:show_calendar('finding.enddate');">
            <img src="/images/picker.gif" valign="middle" width=24 height=22 border=0></span>
        </td>
    </tr>
    <tr >
        <td id="cell 1">
        <input name="button" type="reset" id="button" value="Reset" style="cursor:pointer;">
        <input name="button" type="submit" id="button" value="Search" onClick="dosearch();" style="cursor:pointer;">
        </td>
    </tr>
</table>
</form>
