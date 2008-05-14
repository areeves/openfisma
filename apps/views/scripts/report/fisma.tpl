<!-- repoart.tpl is decieving it is only for the FISMA Report to OMB ONLY -->
<script language="javascript">
function selectr() {
    filter.startdate.style.background="#CCCCCC";
    filter.enddate.style.background="#CCCCCC";
    filter.sy.disabled=true;
    filter.sq.disabled=true;
    filter.startdate.disabled=true;
    filter.enddate.disabled=true;

    if(filter.dr[0].checked) filter.sy.disabled=false;
    if(filter.dr[1].checked) {
        filter.sy.disabled=false;
        filter.sq.disabled=false;
    }
    
    if(filter.dr[2].checked) 
    {
    filter.startdate.style.background="";
    filter.enddate.style.background="";
    filter.startdate.disabled=false;
    filter.enddate.disabled=false;
    }
}
function dosub() {
    if ((filter.dr[0].checked&&filter.sy.value!="")|| 
    (filter.dr[1].checked&&filter.sq.value!=""&&filter.sy.value!="") ||
    (filter.dr[2].checked&&filter.startdate.value!=""&&filter.enddate.value!="")){
    // make sure end date is after start date
        if(filter.dr[2].checked&&filter.startdate.value!="" &&
      filter.enddate.value!="") {
      if(!start_end_dates_ok(filter.startdate.value, filter.enddate.value)) {
        alert("Start date must be before end date");
        return false;
        }
      }
      if (filter.system.value == '') {
          alert("Please choose a system.");
          return false;
      }
      filter.submit();
    }
    else{
    alert("Please choose one analysis date range.");
    return false;
    }
}

function start_end_dates_ok(start_dt, end_dt) {

  // make sure dates are of format mm/dd/yyyy
  if (!(start_dt.match(/^\d\d\/\d\d\/\d\d\d\d/) && end_dt.match(/^\d\d\/\d\d\/\d\d\d\d/))) {
    alert("Dates must be in format mm/dd/yyyy");
    return false;
    }

  // set up a number string of format yyyymmdd for easy comparison
  var fmt_start = start_dt.substr(6,4) + start_dt.substr(3,2) + start_dt.substr(0,2);
  var fmt_end = end_dt.substr(6,4) + end_dt.substr(3,2) + end_dt.substr(0,2);

  return (fmt_end >= fmt_start) ? true : false;
  }

</script>
<br>
<div class="barleft">
<div class="barright">
<p><b>FISMA Report to OMB</b><span><?php echo date('Y-M-D h:i:s:A');?></span>
</div>
</div>
<br>

<form name="filter" method="post" action="/zfentry.php/panel/report/sub/fisma/s/search">
<table width="850"  align="center" border="0" cellpadding="3" cellspacing="1" class="tipframe">
    <tr>
        <td>
            <input name="dr" type="radio" value="y" onClick="javascript:selectr();"
            checked> 
            <b>Yearly</b>    
            <input name="dr" type="radio" value="q"  onClick="javascript:selectr();"
                <?php if('q' == $this->dr){ ?> checked <?php } ?>>  
            <b>Quarterly</b>
        </td>
        <td>
            <input name="dr" type="radio" value="c"  onClick="javascript:selectr();" 
                <?php if('c' == $this->dr){ ?> checked <?php } ?>> 
            <b>Custom</b>
        </td>
    </tr>
    <tr>
        <td width="50%">
            <table width="100%" border="0" cellpadding="3" cellspacing="1"class="tipframe">
                <tr>
                    <td width="47%">
                        <?php echo $this->formSelect('system',$this->criteria['system'],null,$this->system_list);?>
                    </td>
                    <td  width="47%">
                        <?php echo $this->formSelect('sy',$this->criteria['sy'],null,$this->sy_list);?>
                    </td>
                    <td  width="6%">
                        <?php echo $this->formSelect('sq',$this->criteria['sq'],null,$this->sq_list);?>
                    </td>
                </tr>
            </table>
        </td>
        <td width="50%">
            <table width="100%" border="0" cellpadding="3" cellspacing="1" class="tipframe">
                <tr>
                    <td>Start Date</td>
                    <td>&nbsp;</td>
                    <td>From:</td>
                    <td>
                        <input type="text" name="startdate" value="<?php echo $this->startdate;?>" size="10" maxlength="10" value="" onclick="javascript:show_calendar('filter.startdate');" readonly>
                    </td>
                    <td><a href="#" onclick="javascript:show_calendar('filter.startdate');">
                        <img src="/images/picker.gif" width=24 height=22 border=0></a>
                    </td>
                    <td>&nbsp;</td>
                    <td>End Date:</td>
                    <td><input type="text" name="enddate" value="<?php echo $this->enddate;?>" size="10" maxlength="10" value="" onclick="javascript:show_calendar('filter.enddate');" readonly></td>
                    <td><a href="#" onclick="javascript:show_calendar('filter.enddate');">
                        <img src="/images/picker.gif" width=24 height=22 border=0></a>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <input type="hidden" name="t" value="{$t}">
            <input type="hidden" name="sub" value="1">
            <input type="submit" value="Generate Report"  onClick="javascript:dosub();">
        </td>
    </tr>
</table>
</form>
