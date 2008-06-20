<SCRIPT LANGUAGE="JavaScript">
    function submit_export(f_val) {
    // open new window if the request is for a pdf
        if(f_val == 'p') {
            document.exportform.target = "_blank";
        }
        else {
            document.exportform.target = "_self";
        }
        document.exportform.f.value = f_val;
        document.exportform.submit();
    }
</SCRIPT>
<div class="barleft">
<div class="barright">
<p><b>Poam Search Results</b><span>
    <FORM ACTION="/creport.php" METHOD="POST" NAME="exportform">
    <INPUT TYPE="HIDDEN" NAME="f"/>
    <INPUT TYPE="HIDDEN" NAME="t" value="2"/>
    <td width="50%" align="right" bgcolor="#DFE5ED">Export to: 
    <a href="javascript:submit_export('p');" ><img src="/images/pdf.gif" border="0"></a> 
    <a href="javascript:submit_export('e');"><img src="/images/xls.gif" border="0"></a> 
    </FORM></span>
</div></div>
<br>
<input type="hidden" name="action" value="manage">
<table width="95%" align="center" border="0" cellpadding=5" cellspacing="0" class="tbframe">
    <tr align="center">
        <th class="tdc">System
        <th class="tdc">ID#
        <th class="tdc">Description
        <th class="tdc">Type
        <th class="tdc">Status
        <th class="tdc">Source
        <th class="tdc">Server/Database
        <th class="tdc">Location
        <th class="tdc">Risk Level
        <th class="tdc">Recommendation
        <th class="tdc">Corrective Action
        <th class="tdc">ECD
    </tr>
    <?php foreach($this->poam_list as  $row){ ?>
    <tr>
        <td class="tdc" align="center"><?php echo $this->system_list[$row['system_id']];?></td>
        <td class="tdc" align="center"><?php echo $row['id'];?></td>
        <td class="tdc"><?php echo $row['finding_data'];?></td>
        <td class="tdc" align="center"><?php echo $row['type'];?></td>
        <td class="tdc" align="center"><?php echo $row['status'];?></td>
        <td class="tdc" align="center"><?php echo $this->source_list[$row['source_id']];?></td>
        <td class="tdc"><?php echo $row['ip'];?></td>
        <td class="tdc" align="center"><?php echo $this->network_list[$row['network_id']];?></td>
        <td class="tdc" align="center"><?php echo $row['threat_level'];?></td>
        <td class="tdc"><?php echo $row['action_suggested'];?></td>
        <td class="tdc"><?php echo $row['action_planned'];?></td>
        <td class="tdc" align="center"><?php echo $row['action_est_date'];?></td>
    </tr>
    <?php } ?>
</table>
