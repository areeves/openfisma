<script language="javascript">
    $(function(){
        $(':text[name=startdate]').datepicker({dateFormat:'yymmdd',showOn: 'both', buttonImageOnly: true,
            buttonImage: '/images/calendar.gif', buttonText: 'Calendar'});
        
        date = new Date();
        $("span[name=year]").html( date.getFullYear() );
    });
    
    function desShortCutYear(){
        year = $("span[name=year]").html();
        year--;
        $("span[name=year]").html( year );
        $("span[name=year]").parent().attr( 'href', 'http://www.sohu.com' );
        $("span[name=q1]").parent().attr( 'href', 'http://www.sohu.com' );
        $("span[name=q2]").parent().attr( 'href', 'http://www.sohu.com' );
        $("span[name=q3]").parent().attr( 'href', 'http://www.sohu.com' );
        $("span[name=q4]").parent().attr( 'href', 'http://www.sohu.com' );
    }
    
    function incShortCutYear(){
        year = $("span[name=year]").html();
        year++;
        $("span[name=year]").html( year );
        $("span[name=year]").parent().attr( 'href', '' );
        $("span[name=q1]").parent().attr( 'href', '' );
        $("span[name=q2]").parent().attr( 'href', '' );
        $("span[name=q3]").parent().attr( 'href', '' );
        $("span[name=q4]").parent().attr( 'href', '' );
    }
</script>

<!-- repoart.tpl is decieving it is only for the FISMA Report to OMB ONLY -->
<br>
<div class="barleft">
<div class="barright">
<p><b>FISMA Report to OMB</b><span><?php echo date('Y-M-D h:i:s:A');?></span>
</div>
</div>
<br>
<table width="850"  align="center" border="0" cellpadding="3" cellspacing="1" class="tipframe">
<form name="filter" method="post" action="/zfentry.php/panel/report/sub/fisma/s/search">
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
                         <input type="text" name="startdate" value="<?php echo $this->startdate;?>" size="10" maxlength="10" value="">
                    </td>
                    <td><span name="startdate_pic"></span></td>
                    <td>&nbsp;</td>
                    <td>End Date:</td>
                    <td><input type="text" name="enddate" value="<?php echo $this->enddate;?>" size="10" maxlength="10" value=""></td>
                    <td><span onclick="javascript:show_calendar('finding.discovereddate');"><img src="/images/picker.gif" border="0" height="22" width="24"></span></td>
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
</form>
</table>
<div>
    <div style="margin-left:30px;">Generate Report shortcut:&nbsp; 
        <span name="gen_shortcut" >
            <a href="javascript:desShortCutYear();" style="text-decoration: none;" ><<</a>&nbsp
            <a href="" style="text-decoration: none;" ><span name="year"></span></a>&nbsp;
            <a href="" style="text-decoration: none;" ><span name="q1">Q1</span></a>&nbsp;
            <a href="" style="text-decoration: none;" ><span name="q2">Q2</span></a>&nbsp;
            <a href="" style="text-decoration: none;" ><span name="q3">Q3</span></a>&nbsp;
            <a href="" style="text-decoration: none;" ><span name="q4">Q4</span></a>&nbsp;
            <a href="javascript:incShortCutYear();" style="text-decoration: none;">>></a>
        </span>
    </div>
</div>
