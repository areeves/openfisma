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
    <?php foreach($this->rpdata as $row){ ?>
    <tr>
        <td class="tdc" align="center"><?php echo $row['system'];?></td>
        <td class="tdc" align="center"><?php echo $row['poamnum'];?></td>
        <td class="tdc"><?php echo $row['finding'];?></td>
        <td class="tdc" align="center"><?php echo $row['ptype'];?></td>
        <td class="tdc" align="center"><?php echo $row['pstatus'];?></td>
        <td class="tdc" align="center"><?php echo $row['source'];?></td>
        <td class="tdc"><?php echo $row['SD'];?></td>
        <td class="tdc" align="center"><?php echo $row['location'];?></td>
        <td class="tdc" align="center"><?php echo $row['risklevel'];?></td>
        <td class="tdc"><?php echo $row['recommendation'];?></td>
        <td class="tdc"><?php echo $row['correctiveaction'];?></td>
        <td class="tdc" align="center"><?php echo $row['EstimatedCompletionDate'];?></td>
    </tr>
    <?php } ?>
</table>
