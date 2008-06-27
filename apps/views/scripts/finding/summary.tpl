<div class="barleft">
    <div class="barright">
    <p><b>Finding Summary</b></p>
    </div>
</div>

<table align="center" class="tbframe">
    <tr align="center">
        <th>System</td>
        <th>New(Today)</td>
        <th>30(Days)</td>
        <th>60(Days)</td>
        <th>More Days</td>
        <th>Remediation</td>
        <th>Closed</td>
        <th>Total</td>
    </tr>
<?php
foreach($this->statistic as $sys_id => $row){
    $search_url_base = "/zfentry.php/panel/finding/sub/searchbox/s/search/system_id/$sys_id";
?>
    <tr>
        <td class="tdc" align="left">&nbsp;
            <?php echo $row['NAME']; ?>
        </td>
        <td class="tdc" >&nbsp;
            <a href='<?php echo "{$search_url_base}/status/NEW/discovered_date_begin/".
                $this->range['today']['from']->toString('Ymd'). 
                "/discovered_date_end/".
                $this->range['today']['to']->toString('Ymd'); ?>'>
            <?php echo $row['NEW']['today']; ?></a>
        </td>
        <td class="tdc" >&nbsp;
            <a href="<?php echo "{$search_url_base}/status/NEW/discovered_date_begin/".
                $this->range['last30']['from']->toString('Ymd').
                "/discovered_date_end/".
                $this->range['last30']['to']->toString('Ymd'); 
             ?>">
            <?php echo $row['NEW']['last30day']; ?></a>
        </td>
        <td class="tdc" >&nbsp;
            <a href="<?php echo "{$search_url_base}/status/NEW/discovered_date_begin/".
                $this->range['last60']['from']->toString('Ymd').
                "/discovered_date_end/".
                $this->range['last60']['to']->toString('Ymd'); 
             ?>">
            <?php echo $row['NEW']['last2nd30day']; ?></a>
        </td>
        <td class="tdc" >&nbsp;
            <a href="<?php echo "{$search_url_base}/status/NEW/discovered_date_end/".
                $this->range['after60']['to']->toString('Ymd'); 
             ?>">
            <?php echo $row['NEW']['before60day']; ?></a>
        </td>
        <td class="tdc" >&nbsp;
            <a href="<?php echo "{$search_url_base}/status/REMEDIATION";?>">
                <?php echo $row['REMEDIATION']; ?>
            </a>
        </td>
        <td class="tdc" >&nbsp;
            <a href="<?php echo "{$search_url_base}/status/CLOSED";?>">
                <?php echo $row['CLOSED']; ?>
            </a>
        </td>
        <td class="tdc" >&nbsp;
            <a href="<?php echo "{$search_url_base}";?>">
                <?php echo $row['CLOSED']['total']+
                       $row['REMEDIATION']['total']+
                       $row['NEW']['total'] ; ?>
            </a>
        </td>
    </tr>
<?php
}
?>
</table>

