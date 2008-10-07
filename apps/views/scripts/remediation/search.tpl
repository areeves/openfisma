<div class="barleft">
<div class="barright">
    <p><b>Remediation Search Results</b>
        <span>
            <?php echo $this->links['all'];?>
        </span>
    </p>
</div>
</div>
    <table width="100%" class="tbframe">
        <th nowrap>ID 
            <a href="<?php echo $this->url;?>/sortby/id/order/ASC"><img src="/images/up_arrow.gif" border="0"></a>
            <a href="<?php echo $this->url;?>/sortby/id/order/DESC"><img src="/images/down_arrow.gif" border="0"></a>
        </th>
        <th nowrap>Source </th>
        <th nowrap>System </th>
        <th nowrap>Type </th>
        <th nowrap>Status </th>
        <th nowrap>Finding </th>
        <th nowrap>ECD 
           <a href="<?php echo $this->url;?>/sortby/action_est_date/order/ASC"><img src="/images/up_arrow.gif" border="0"></a>
           <a href="<?php echo $this->url;?>/sortby/action_est_date/order/DESC"><img src="/images/down_arrow.gif" border="0"></a>
        </th>
        <th nowrap>View</th>
        </tr>
        <!-- REMEDIATION ROWS -->
        <?php
            foreach($this->list as $row){
            $finding_data = strlen($row['finding_data'])>120?substr($row['finding_data'],0,120)."...":$row['finding_data'];
        ?>
        <tr>
            <td align='center' class='tdc'><?php echo $row['id'];?></td>
            <td align='center' class='tdc' nowrap>
            <?php 
                    echoDefault($this->sources[$row['source_id']],'Missing Source id');
            ?>
            </td>
            <td align='center' class='tdc'><?php echo $this->systems[$row['system_id']];?></td>
            <td align='center' class='tdc' nowrap><?php echo $row['type'];?></td>
            <td align='center' class='tdc' nowrap><?php echo $row['status'];?></td>
            <td align='left'   class='tdc'><?php echo $finding_data;?></td>
            <td align='center' class='tdc' nowrap><?php echo $row['action_est_date'];?></td> 
            <td align="center" valign='middle' class='tdc'><a href="/panel/remediation/sub/view/id/<?php echo $row['id'];?>"><img src="/images/view.gif" border="0"></a></td>
        </tr>
        <?php } ?>
    </table>
