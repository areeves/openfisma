<?php
    $status_list = array("NEW"=>'NEW',
                 "REMEDIATION"=>"REMEDIATION",
                        'OPEN'=>'-- OPEN',
                          'EN'=>'-- EN',
                          'EP'=>'-- EP',
                      "CLOSED"=>'Closed',
                           0  =>'--Any--');
    $this->source[0] = '--Any--';
    $this->system[0] = '--Any--';
    $this->network[0] = '--Any--';

?>
<div class="barleft">
<div class="barright">
<p><b>Search Findings</b></p>
</div>
</div>
<br>

<form name="finding" method="post" action="/zfentry.php/panel/finding/sub/searchbox/s/search/">
<table align="center" border="0" cellpadding="5" cellspacing="1" class="tipframe">
    <tr >
        <td id="cell 1" align="right">System:</td>
        <td id="cell 2" align="left">
        <?php echo $this->formSelect('system_id', 
                                    nullGet($this->criteria['system_id'],0),
                                        null, $this->system); ?>
        </td>
        <td id="cell 3" align="right">Source:</td>
        <td id="cell 4" align="left">
        <?php echo $this->formSelect('source_id', 
                                    nullGet($this->criteria['source_id'],0),
                                        null, $this->source); ?>
        </td>
    </tr>
    <tr >
        <td align="right">Network:</td>
        <td align="left">
        <?php echo $this->formSelect('network_id', 
                                    nullGet($this->criteria['network_id'],0),
                                        null, $this->network); ?>
        </td>
        <td align="right">Status:</td>
        <td align="left">
        <?php echo $this->formSelect('status', 
                                    nullGet($this->criteria['status'],0),
                                        null, $status_list); ?>
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
        <td align="right">Vulnerability:</td>
        <td align="left"><input type="text" name="vuln" value="<?php echo $this->criteria['vuln'];?>" maxlength="20"></td>
        <td align="right">Product:</td>
        <td align="left"><input type="text" name="product" value="<?php echo $this->criteria['product']; ?>" maxlength="20"></td>
    </tr>
    <tr >
        <td align="right">Date Discovered From: </td>
        <td align="left"><input type="text" class="date" name="discovered_date_begin" size="12" maxlength="10"
        value="<?php $ts = nullGet($this->criteria['discovered_date_begin'],'');
                     if($ts instanceof Zend_Date){
                        $ts = $ts->toString('Y-m-d');
                     }
                     echo $ts;
               ?>" />             
        </td>
        <td align="right">To: </td>
        <td align="left"><input type="text" class="date" name="discovered_date_end" size="12" maxlength="10"
        value="<?php $ts = nullGet($this->criteria['discovered_date_end'],'');
                     if($ts instanceof Zend_Date){
                         $ts = $ts->toString('Y-m-d');
                     }
                     echo $ts;
               ?>" />
        </td>
    </tr>
    <tr >
        <td id="cell 1">
        <input name="button" type="reset" id="button" value="Reset" style="cursor:pointer;">
        <input name="button" type="submit" id="button" value="Search"  style="cursor:pointer;">
        </td>
    </tr>
</table>
</form>
