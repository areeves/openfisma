<!--
  --   FIPS 199 Category report
  -- Input:
  --  rpdata - array containing two rowsets
  --   rpdata[0] - set of system detail data rows
  --   rpdata[1] - row of LOW/MODERATE/HIGH totals
  -- 
-->
<table width="95%" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr align="left" width="60%" valign="middle">
        <td>
            <!-- Summary Table -->
            <table width="80%" border="0" c:ellpadding="0" cellspacing="0" class="tbframe" >
                <tr align="center">
                    <th width="20%"><b>FIPS 199 Category</b></th>
                    <th width="20%"><b>Low</b></th>
                    <th width="20%"><b>Moderate</b></th>
                    <th width="20%"><b>High</b></th>
                </tr>
                <tr align="center">
                    <td class="tdc"><b>Total Systems</b></td>
                    <td class="tdc"><?php echo $this->rpdata[1]['LOW'];?></td>
                    <td class="tdc"><?php echo $this->rpdata[1]['MODERATE'];?></td>
                    <td class="tdc"><?php echo $this->rpdata[1]['HIGH'];?></td>
                </tr>
            </table>
        </td>
        <td width="40%">
            <img src=piechart.php?data[]={$rpdata[1].LOW}&data[]={$rpdata[1].MODERATE}&data[]={$rpdata[1].HIGH}>
        </td>
    </tr>    
</table>

<br>

<!-- Details table -->
<table width="95%" align="center" border="0" cellpadding="5" cellspacing="0" class="tbframe"  >
    <!-- table header row -->
    <tr align="center">
        <th>System Name</th>
        <th>System Type</th>
        <th>FIPS 199 Category</th>
        <th>Confidentiality</th>
        <th>Integrity</th>
        <th>Availability</th>
        <th nowrap>Last Inventory Update</th>
    </tr>
    <!-- table detail rows -->
    <?php foreach($this->rpdata[0] as $item){ ?>
     <tr align="center">
        <td align="left" class="tdc"><?php echo $item['name'];?></td>
        <td class="tdc"><?php echo $item['type'];?></td>
        <td class="tdc"><?php echo $item['fips'];?></td>
        <td class="tdc"><?php echo $item['conf'];?></td>
        <td class="tdc"><?php echo $item['integ'];?></td>
        <td class="tdc"><?php echo $item['avail'];?></td>
        <td class="tdc">n/a</td>
    </tr>
    <?php } ?>
</table>

