<?php 
$type_list = array('' =>'Please Select Report',
                   '1'=>'NIST Baseline Security Controls Report',
                   '2'=>'FIPS 199 Categorization Breakdown',
                   '3'=>'Products with Open Vulnerabilities',
                   '4'=>'Software Discovered Through Vulnerability Assessments',
                   '5'=>'Total # of Systems /w Open Vulnerabilities');
$url = "/zfentry.php/panel/report/sub/general/s/search";
?>
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
                        <?php echo $this->formSelect('type',$this->type,null,$type_list);?>
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
<div class="barleft">
<div class="barright">
<p><b>Report: <?php echo $type_list[$this->type];?></b>
    <span>
    <a target='_blank' href="<?php echo $url.'/type/'.$this->type.'/format/pdf'; ?>"><img src="/images/pdf.gif" border="0"></a>
    <a href="<?php echo $url.'/format/xls'; ?>"><img src="/images/xls.gif" border="0"></a>
    </span>
</div>
</div>
<?php } ?>
