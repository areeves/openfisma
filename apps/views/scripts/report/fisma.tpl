<script language="javascript">
    date = new Date();
    $("span[name=year]").html( date.getFullYear() );
    shortcut(0);
    function shortcut(step){
        if( !isFinite(step) ){
            step = 0;
        }
        var year = $("span[name=year]").html();
        year = Number(year) + Number(step);
        var url = '/zfentry.php/panel/report/sub/fisma/s/search/y/'+year+'/';
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
<p><b>FISMA Report to OMB</b></p>
</div>
</div>
<form name="filter" method="post" action="/zfentry.php/panel/report/sub/fisma/s/search">
<table width="850"  align="center" border="0" cellpadding="3" cellspacing="1" class="tipframe">
    <tr>
        <td >
            <?php 
                $this->system_list[0] = 'All Systems';
                echo $this->formSelect('system',nullGet($this->criteria['system_id'],0),null,$this->system_list);
            ?>
        </td>
        <td>From:<input type="text" class="date" name="startdate" value="<?php echo nullGet($this->criteria['startdate'],'');?>" size="10" maxlength="10" >
        </td>
        <td>To:
        <input type="text" class="date" name="enddate" value="<?php echo nullGet($this->criteria['enddate'],'');?>" size="10" maxlength="10"></td>
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
