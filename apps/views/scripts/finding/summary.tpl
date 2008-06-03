<div class="barleft">
    <div class="barright">
    <p><b>Finding Summary</b></p>
    </div>
</div>

<table align="center" class="tbframe">
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
    $search_url_base = "/zfentry.php/panel/finding/sub/searchbox/s/search/system/$sys_id";
    $total = $row['OPEN']['total'];
?>
    <tr >
        <td  class="tdc" align="left">&nbsp;
            <?php echo $row['NAME']?>
        </td>
        <td class="tdc" >&nbsp;
            <a href="<?php echo "{$search_url_base}/status/OPEN/from/{$this->range['today']['from']}/to/{$this->range['today']['to']}"; ?>">
            <?php echo $row['OPEN']['today']; ?></a>
        </td>
        <td class="tdc" >&nbsp;
            <a href="<?php echo "{$search_url_base}/status/OPEN/from/{$this->range['last30']['from']}/to/{$this->range['last30']['to']}";?>">
            <?php echo $row['OPEN']['last30day']; ?></a>
        </td>
        <td class="tdc" >&nbsp;
            <a href="<?php echo "{$search_url_base}/status/OPEN/from/{$this->range['last60']['from']}/to/{$this->range['last30']['to']}";?>">
            <?php echo $row['OPEN']['last2nd30day']; ?></a>
        </td>
        <td class="tdc" >&nbsp;
            <a href="<?php echo "{$search_url_base}/status/OPEN/from/{$this->range['after60']['from']}/to/{$this->range['after60']['to']}";?>">
            <?php echo $row['OPEN']['before60day']; ?></a>
        </td>
        <td class="tdc" >&nbsp;
            <a href="<?php echo "{$search_url_base}/status/REMEDIATION";?>"><?php echo $row['REMEDIATION']['total']; ?></a>
        </td>
        <td class="tdc" >&nbsp;
            <a href="<?php echo "{$search_url_base}/status/CLOSED";?>"><?php echo $row['CLOSED']['total']; ?></a>
        </td>
        <td class="tdc" >&nbsp;
            <a href="<?php echo "{$search_url_base}";?>"><?php echo $row['CLOSED']['total']+
                       $row['REMEDIATION']['total']+
                       $row['OPEN']['total'] ; ?>
        </td></a>
    </tr>
<?php
}
?>
</table>

