<?php
    $url = "/zfentry.php/panel/report/sub/fisma/s/search";
?>
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
<p><b>FISMA Report to OMB:<?php echo $this->startdate;?>throw <?php echo $this->enddate;?></b>    <span>
    Export to:
    <a target='_blank' href="<?php echo $url.'/format/pdf'; ?>"><img src="/images/pdf.gif" border="0"></a>
    <a href="<?php echo $url.'/format/xls'; ?>"><img src="/images/xls.gif" border="0"></a>
    </span>
</div>
</div>
<br>
<table width="95%" align="center" class="tbframe">
    <tr align="center">
        <td colspan="5"  bgcolor="#DFE5ED"><b>FISMA Report to OMB: POA&M Status Report</b></td>
    </tr>
    <tr align="center">
        <td colspan="5"  bgcolor="#DFE5ED">
            <table width="100%" border="0" cellspacing="0" cellpadding="4">
                <tr align="center">
                    <th width="50%">POA&M Status Information
                    <th width="15%">Agency Wide
                    <th width="15%">System
                    <th width="15%">Total
                    <th width="15%" nowrap>Brief Explanation</tr>
                <tr>
                    <td width="50%" class="tdc">
                        A. Total number of weaknesses identified at the start of the reporting period
                    </td>
                    <td width="15%" class="tdc"><?php echo $this->AAW;?></td>
                    <td width="15%" class="tdc"><?php echo $this->AS;?></td>
                    <td width="15%" class="tdc"><?php echo $this->AAW+$this->AS;?></td>
                    <td width="15%" bgcolor="#DFDFDF" class="tdc">&nbsp;</td>
                </tr>
                <tr>
                    <td width="50%" class="tdc">
                        B. Number of weaknesses for which corrective action was completed on time(including testing) by the end of the reporting period
                    </td>
                    <td width="15%" class="tdc"><?php echo $this->BAW;?></td>
                    <td width="15%" class="tdc"><?php echo $this->BS;?></td>
                    <td width="15%" class="tdc"><?php echo $this->BAW+$this->BS;?></td>
                    <td width="15%" bgcolor="#DFDFDF" class="tdc">&nbsp;</td>
                </tr>
                <tr>
                    <td width="50%" class="tdc">
                        C. Number of weaknesses for which corrective action is ongoing and is on track to complete as originally scheduled
                    </td>
                    <td width="15%" class="tdc"><?php echo $this->CAW;?></td>
                    <td width="15%" class="tdc"><?php echo $this->CS;?></td>
                    <td width="15%" class="tdc"><?php echo $this->CAW+$this->CS;?></td>
                    <td width="15%" bgcolor="#DFDFDF" class="tdc">&nbsp;</td>
                </tr>
                <tr>
                    <td width="50%" class="tdc">
                        D. Number of weaknesses for which corrective action has been delayed including a brief explanation for the delay
                    </td>
                    <td width="15%" class="tdc"><?php echo $this->DAW;?></td>
                    <td width="15%" class="tdc"><?php echo $this->DS;?></td>
                    <td width="15%" class="tdc"><?php echo $this->DAW+$this->DS;?></td>
                    <td width="15%" class="tdc">&nbsp;</td>
                </tr>
                <tr>
                    <td width="50%" class="tdc">
                        E. Number of weaknesses discovered following the last POA&M update and a brief Explanation of how they were identified (e.g., agency review, IG evaluation, etc.)
                    </td>
                    <td width="15%" class="tdc"><?php echo $this->EAW;?></td>
                    <td width="15%" class="tdc"><?php echo $this->ES;?></td>
                    <td width="15%" class="tdc"><?php echo $this->EAW+$this->ES;?></td>
                    <td width="15%" class="tdc">&nbsp;</td>
                </tr>
                <tr>
                    <td width="50%" class="tdc" nowrap>
                        Total number of weaknesses remaining at the end of the reporting period
                    </td>
                    <td width="15%" class="tdc"><?php echo $this->FAW;?></td>
                    <td width="15%" class="tdc"><?php echo $this->FS;?></td>
                    <td width="15%" class="tdc"><?php echo $this->FAW+$this->FS;?></td>
                    <td width="15%" bgcolor="#DFDFDF" class="tdc">&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<script language="javascript">
selectr();
</script>
