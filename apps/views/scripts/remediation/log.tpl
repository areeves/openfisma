<!-- ------------------------------------------------------------------------ -->
<!-- Heading Block -->
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="13"><img src="/images/left_circle.gif" border="0"></td>
        <td bgcolor="#DFE5ED"><b>Audit Log</b></td>
        <td bgcolor="#DFE5ED" align="right"></td>
        <td width="13"><img src="/images/right_circle.gif" border="0"></td>
    </tr>
</table>
<!-- End Heading Block -->

    <br>

<!-- COMMENT TABLE -->
<!-- <th align="left">Logs <i>({$num_logs} total)</i></th> -->

    <!-- loop through the logs -->
    <?php if($this->num_logs > 0){ ?>
<table border="0" align="center" cellpadding="5" cellspacing="1" width="95%" class="tbframe">
    <tr>
        <th>Timestamp</td>
        <th>User</td>
        <th>Event</td>
        <th>Description</td>
    </tr>
    <?php foreach($this->logs as $row){ ?>
    <tr>
        <td class="tdc"><?php echo $row['time'];?></td>
        <td class="tdc"><?php echo $row['user_name'];?></td>
        <td class="tdc"><?php echo $row['event'];?></td>
        <td class="tdc"><?php echo $row['description'];?></td>
    </tr>
    <?php } ?>
</table>
<?php }?>
<!-- COMMENT TABLE -->

