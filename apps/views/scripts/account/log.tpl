<div class="barleft">
<div class="barright">
<p><b>User Logs</b><span></span></p>
</div>
</div>
<table align="center" cellpadding="5" cellspacing="1" width="98%" class="tbframe">
    <tr>
        <th width="20%">Timestamp</td>
        <th width="15%">User</td>
        <th width="20%">Event</td>
        <th>Message</td>
    </tr>
<?php
    foreach ($this->logList as $row) {
?>
    <tr>
        <td class="tdc"><?php echo $row['timestamp'];?></td>
        <td class="tdc"><?php echo $row['account'];?></td>
        <td class="tdc"><?php echo $row['event'];?></td>
        <td class="tdc"><?php echo $row['message']; ?></td>
    </tr>
<?php } ?>
</table>
