<div class="barleft">
<div class="barright">
<p><b>Finding Summary</b><span><?PHP echo date('Y-M-D h:i:s:A');?></span></p
</div>
</div>
<br>
<table width="95%" align="center" >
    <tr>
        <td>
            <table align="center" border="0" cellpadding="5" cellspacing="0" class="tbframe">
                <tr align="center">
                    <th>System</td>
                    <th>Open(Today)</td>
                    <th>30(Days)</td>
                    <th>60(Days)</td>
                    <th>More Days</td>
                    <th>Remediation</td>
                    <th>Closed</td>
                    <th>Total</td>
                </tr>
<?php
foreach($this->summary_data as $row){
?>
                <tr align="center">
                    <td class="tdc" align="left">&nbsp;<?php echo $row['system']?></td>
                    <td class="tdc">&nbsp;<?php echo isset($row['open'])?$row['open']:'' ?>&nbsp;</td>
                    <td class="tdc">&nbsp;<?php echo isset($row['thirty'])?$row['thirty']:'' ?>&nbsp;</td>
                    <td class="tdc">&nbsp;<?php echo isset($row['sixty'])?$row['sixty']:'' ?>&nbsp;</td>
                    <td class="tdc">&nbsp;<?php echo isset($row['ninety'])?$row['ninety']:'' ?>&nbsp;</td>
                    <td class="tdc">&nbsp;<?php echo isset($row['reme'])?$row['reme']:'' ?>&nbsp;</td>
                    <td class="tdc">&nbsp;<?php echo isset($row['closed'])?$row['closed']:'' ?>&nbsp;</td>
                    <td class="tdc">&nbsp;<?php echo isset($row['total'])?$row['total']:'' ?>&nbsp;</td>
                </tr>
<?php
}
?>
            </table>
        </td>
    </tr>
</table>

