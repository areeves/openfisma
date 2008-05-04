<div class="barleft">
<div class="barright">
<p><b>Remediation Search Results</b><span><?php echo date('Y-M-D h:i:s:A');?></span></p>
</div>
</div>
</table>
<!-- End Heading Block -->

<!-- Remediation Summary Table -->
<table width="95" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <!-- Pagination-->
            <?php echo $this->links['all'];?>
            <!--End Pagination -->
        </td>
    </tr>
    <tr>
        <td>
        <table width="100%" border="1" cellpadding="5" cellspacing="0" class="tbframe">
            <form  name="order_by_ID" action='remediation.php' method='POST'>
            <th nowrap>
                <input type='hidden' name='remediation_id'          value='{$list[row].poam_id}'>
                <input type='hidden' name='remediation_ids'         value='{$remediation_ids}'>
                <input type='hidden' name='filter_source'           value='{$filter_source}'>
                <input type='hidden' name='filter_system'           value='{$filter_system}'>
                <input type='hidden' name='filter_status'           value='{$filter_status}'>
                <input type='hidden' name='filter_type'             value='{$filter_type}'>
                <input type='hidden' name='filter_startdate'        value='{$filter_startdate}'>
                <input type='hidden' name='filter_enddate'          value='{$filter_enddate}'>
                <input type='hidden' name='filter_startcreatedate'  value='{$filter_startcreatedate}'>
                <input type='hidden' name='filter_endcreatedate'    value='{$filter_endcreatedate}'>
                <input type='hidden' name='sort_by'        value='remediation_id'> 
                <input type='hidden' name='sort_order' >          ID 
                <input type='image'  src='/images/up_arrow.gif'   onClick="order_page(11)"> 
                <input type='image'  src='/images/down_arrow.gif' onClick="order_page(12)">          
            </th>
            <th nowrap>Source 
                <input type='image'  src='/images/up_arrow.gif'   onClick="order_page(21)"> 
                <input type='image'  src='/images/down_arrow.gif' onClick="order_page(22)">          
            </th>
            <th nowrap>System 
                <input type='image'  src='/images/up_arrow.gif'   onClick="order_page(41)"> 
                <input type='image'  src='/images/down_arrow.gif' onClick="order_page(42)">          
            </th>
            <th nowrap>Type 
                <input type='image'  src='/images/up_arrow.gif'   onClick="order_page(51)"> 
                <input type='image'  src='/images/down_arrow.gif' onClick="order_page(52)">          
            </th>
            <th nowrap>Status 
                <input type='image'  src='/images/up_arrow.gif'   onClick="order_page(61)"> 
                <input type='image'  src='/images/down_arrow.gif' onClick="order_page(62)">
            </th>
            <th nowrap>Finding
                <input type='image'  src='/images/up_arrow.gif'   onClick="order_page(71)">
                <input type='image'  src='/images/down_arrow.gif' onClick="order_page(72)">
            </th>
            <th nowrap>ECD 
                <input type='image'  src='/images/up_arrow.gif'   onClick="order_page(81)"> 
                <input type='image'  src='/images/down_arrow.gif' onClick="order_page(82)">          
            </th>
                </form>
            <th nowrap>View</th>
            </tr>
            <!-- REMEDIATION ROWS -->
            <?php foreach($this->summary_list as $row){
                $finding_data = strlen($row['finding_data'])>120?substr($row['finding_data'],0,120)."...":$row['finding_data'];
                $poam_id = null == $row['legacy_poam_id']?$row['poam_id']:$row['poam_id']."(".$row['legacy_poam_id'].")";
            ?>
            <tr>
                <td align='center' class='tdc'><?php echo $poam_id;?></td>
                <td align='center' class='tdc' nowrap><?php echo $row['source_nickname'];?></td>
                <td align='center' class='tdc'><?php echo $row['action_owner_nickname'];?></td>
                <td align='center' class='tdc' nowrap><?php echo $row['poam_type'];?></td>
                <td align='center' class='tdc' nowrap><?php echo $row['poam_status'];?></td>
                <td align='left'   class='tdc'><?php echo $finding_data;?></td>
                <td align='center' class='tdc' nowrap><?php echo $row['poam_action_date_est'];?></td> 
                <td align="center" valign='middle' class='tdc'><a href='/zfentry.php/panel/remediation/sub/view/id/<?php echo $row['poam_id'];?>'><img src='/images/view.gif' border="0"></a></td>
            </tr>
            <?php } ?>
        </table>
        </td>
    </tr>
</table>
