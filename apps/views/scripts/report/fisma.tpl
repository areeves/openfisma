<script language="javascript">
    $(function(){
        $(':text').datepicker({dateFormat:'yymmdd',showOn: 'both', buttonImageOnly: true,
            buttonImage: '/images/calendar.gif', buttonText: 'Calendar'});
        
        date = new Date();
        $("span[name=year]").html( date.getFullYear() );
        shortcut(0);
    });
    
    function shortcut(step){
        if( !isFinite(step) ){
            step = 0;
        }
        var year = $("span[name=year]").html();
        year = Number(year) + Number(step);
        var url = '/zfentry.php/report/fisma/y/'+year+'/';
        $("span[name=year]").html( year );
        $("span[name=year]").parent().attr( 'href', url);
        $("span[name=q1]").parent().attr( 'href', url+'q/1/' );
        $("span[name=q2]").parent().attr( 'href', url+'q/2/' );
        $("span[name=q3]").parent().attr( 'href', url+'q/3/' );
        $("span[name=q4]").parent().attr( 'href', url+'q/4/' );
    }
</script>

<div class="barleft">
<div class="barright">
<p><b>FISMA Report to OMB</b><span><?php echo date('Y-M-D h:i:s:A');?></span>
</div>
</div>
<br>
<table width="850"  align="center" border="0" cellpadding="3" cellspacing="1" class="tipframe">
<form name="filter" method="post" action="/zfentry.php/panel/report/sub/fisma/s/search">
    <tr>
        <td >
            <?php 
                asort($this->system_list);
                $this->system_list[0] = 'All Systems';
                echo $this->formSelect('system',0,null,$this->system_list);
            ?>
        </td>
        <td>From:<input type="text" class="date" name="startdate" value="<?php echo $this->startdate;?>" size="10" maxlength="10" >
        </td>
        <td>To:
        <input type="text" class="date" name="enddate" value="<?php echo $this->enddate;?>" size="10" maxlength="10"></td>
        <td >
            <input type="submit" value="Generate Report">
        </td>
    </tr>
</table>
</form>
<div>
    <div style="margin-left:30px;">Generate Report shortcut:&nbsp; 
        <span name="gen_shortcut" >
            <a href="javascript:shortcut(-1);" style="text-decoration: none;" ><<</a>&nbsp
            <a href="" style="text-decoration: none;" ><span name="year"></span></a>&nbsp;
            <a href="" style="text-decoration: none;" ><span name="q1">Q1</span></a>&nbsp;
            <a href="" style="text-decoration: none;" ><span name="q2">Q2</span></a>&nbsp;
            <a href="" style="text-decoration: none;" ><span name="q3">Q3</span></a>&nbsp;
            <a href="" style="text-decoration: none;" ><span name="q4">Q4</span></a>&nbsp;
            <a href="javascript:shortcut(1);" style="text-decoration: none;">>></a>
        </span>
    </div>
</div>
