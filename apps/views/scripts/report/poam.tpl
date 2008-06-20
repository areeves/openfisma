<?php
    $year_list = array(0     =>'All Fiscal Year',
                       '2005' =>'2005',
                       '2006' =>'2006',
                       '2007' =>'2007',
                       '2008' =>'2008',
                       '2009' =>'2009');
    $status_list = array('CLOSED' =>'Closed',
                         'OPEN'   =>'Open',
                         0        =>'All Status');
    $type_list = array('CAP'  =>'CAP',
                       'FP'   =>'FP',
                       'AR'   =>'AR',
                       0      =>'All Type');
    $this->system_list[0] ='--Any--';
    asort($this->system_list);
    $this->source_list[0] ='--Any--';
    asort($this->source_list);
?>
<div class="barleft">
<div class="barright">
<p><b>Reports : POA&amp;M Reports</b><span><?php echo date('Y-M-D h:i:s:A');?></span>
</div>
</div>

<form name="filter" method="post" action="/zfentry.php/panel/report/sub/poam/s/search">
<input type="hidden" name="action" value="filter">
<table width="95%" align="center" border="0" cellpadding="5" cellspacing="1" class="tipframe">
    <tr>
        <td width="6%" height="47"><b>System </b></td>
        <td width="21%">
        <?php echo $this->formSelect('system_id',
                                     nullGet($this->criteria['system_id'],0), 
                                     null,$this->system_list);?>
        </td>
        <td width="6%"><b>Source</b></td>
        <td width="18%">
        <?php echo $this->formSelect('source_id',
                                     nullGet($this->criteria['source_id'],0), 
                                     null,$this->source_list);?>
        </td>
        <td width="9%"><b>Fiscal Year</b></td>
        <td width="40%">
        <?php echo $this->formSelect('year',
                                     nullGet($this->criteria['year'],0), 
                                     null,$year_list);?>
        </td>
    </tr>
    <tr>
        <td height="30"><b>Type</b></td>
        <td>
        <?php echo $this->formSelect('type',
                                    nullGet($this->criteria['type'],0),
                                    null,$type_list);?>
        </td>
        <td><b>Status</b></td>
        <td colspan="3">
        <?php echo $this->formSelect('status',
                                    nullGet($this->criteria['status'],0),
                                    null,$status_list);?>
        </td>
    </tr>
    <?php
    if('overdue' == $this->flag){ ?>
    <tr>
        <td height="26"><b>Status</b></td>
        <td><?php echo $this->formSelect('status',$this->criteria['status'],null,$this->status_list);?>
        </td>
        <td><b>Overdue</b></td>
        <td colspan="3">
        <?php echo $this->formSelect('overdue',$this->criteria['overdue'],null,$this->overdue_list);?>
        </td>
    </tr>
    <?php } ?>
    <tr>
        <td height="39" colspan="6"><input type="submit" name="search" value="Generate">
    </tr>
</table>
</form>
