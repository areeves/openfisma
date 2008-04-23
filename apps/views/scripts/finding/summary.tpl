<div class="barleft">
<div class="barright">
<p><b>Finding Summary</b><span><?PHP echo date('Y-M-D h:i:s:A');?></span></p
</div>
</div>
<table align="center" border="1" cellpadding="5" cellspacing="0" class="tbframe">
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
foreach($this->statistic as $sys_id => $row){
    $total = $row['OPEN']['total'];
?>
    <tr >
        <td  align="left">&nbsp;
            <?php echo $row['NAME']?>
        </td>
        <td >&nbsp;
            <?php echo $row['OPEN']['today']; ?>
        </td>
        <td >&nbsp;
            <?php echo $row['OPEN']['last30day']; ?>
        </td>
        <td >&nbsp;
            <?php echo $row['OPEN']['last2nd30day']; ?>
        </td>
        <td >&nbsp;
            <?php echo $row['OPEN']['before60day']; ?>
        </td>
        <td >&nbsp;
            <?php echo $row['REMEDIATION']['total']; ?>
        </td>
        <td >&nbsp;
            <?php echo $row['CLOSED']['total']; ?>
        </td>
        <td >&nbsp;
            <?php echo $row['CLOSED']['total']+
                       $row['REMEDIATION']['total']+
                       $row['OPEN']['total'] ; ?>
        </td>
    </tr>
<?php
}
?>
</table>

