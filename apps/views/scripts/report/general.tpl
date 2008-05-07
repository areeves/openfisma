<SCRIPT LANGUAGE = "JAVASCRIPT">
function  dosub() {
    if (filter.type.value!=""){
    filter.submit();
    }
    else{
    alert("Please choose one report.");
    return false;
    }
}
</SCRIPT>
<br>
<div class="barleft">
<div class="barright">        
<p><b>Reports : General Reports</b><span><?php echo date('Y-M-D h:i:s:A');?></span>
</div>
</div>
<br>
<form name="filter" method="post" action="/zfentry.php/panel/report/sub/general/s/search">
<table width="95%" align="center" border="0">
    <tr>
        <td>
            <table cellpadding="5" class="tipframe">
                <tr>
                    <td><b>Report</b></td>
                    <td>
                        <?php echo $this->formSelect('type',$this->criteria['type'],null,$this->type_list);?>
                    </td>
                    <td>
                        <input type="submit" value="Generate"  onClick="javascript:dosub();">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</form>
<?php if(!empty($this->type)){ ?>
<br>
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="0"><img src="/images/left_circle.gif" border="0"></td>
        <td width="50%" bgcolor="#DFE5ED"><b>Report: <?php echo $this->type_list[$this->type];?></b></td>
        <!-- Set up FORM + Javascript to POST data based on image selected.
        ** This hides passed variables from the URL string.-->
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
        <FORM ACTION="/creport.php" METHOD="POST" NAME="exportform">
        <INPUT TYPE="HIDDEN" NAME="f"/>
        <INPUT TYPE="HIDDEN" NAME="t" value="3<?php echo $this->type;?>"/>
        <td width="50%" align="right" bgcolor="#DFE5ED">Export to:
            <a href="javascript:submit_export('p');" ><img src="/images/pdf.gif" border="0"></a>
        <a href="javascript:submit_export('e');"><img src="/images/xls.gif" border="0"></a> 
        </td>
        </FORM>
        <td width="0"><img src="/images/right_circle.gif" border="0"></td>
    </tr>
</table>
<br>
<?php } ?>
