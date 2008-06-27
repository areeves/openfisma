<script language="javascript">
function delok(entryname)
{
    var str = "Are you sure that you want to delete this " + entryname + "?";
    if(confirm(str) == true){
        return true;
    }
    return false;
}
</script>
<div class="barleft">
<div class="barright">
<p><b>Administration: Systems List</b>
</div>
</div>
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0" class="tbframe">
<tr align="center">
    <th>System Name</th>
    <th>NickName</th>
    <th>Primary Office</th>
    <th>Confidentiality</th>
    <th>Integrity</th>
    <th>Availability</th>
    <th>Type</th>
    <?php if(isAllow('admin_systems','update')){
              echo'<th>Edit</td>';
          } 
          if(isAllow('admin_systems','read')){
              echo'<th>View</td>';
          }
          if(isAllow('admin_systems','delete')){
              echo'<th>Del</td>';
          }
    ?>
</tr>
<?php foreach($this->system_list as $system){ ?>
<tr>
    <td class="tdc">&nbsp;<?php echo $system['name'];?></td>
    <td class="tdc">&nbsp;<?php echo $system['nickname'];?></td>
    <td class="tdc">&nbsp;<?php echo $system['primary_office'];?></td>
    <td class="tdc">&nbsp;<?php echo $system['confidentiality'];?></td>
    <td class="tdc">&nbsp;<?php echo $system['integrity'];?></td>
    <td class="tdc">&nbsp;<?php echo $system['availability'];?></td>
    <td class="tdc">&nbsp;<?php echo $system['type'];?></td>
    <?php if(isAllow('admin_systems','update')){ ?>
    <td class="thc" align="center">
        <a href="/zfentry.php/panel/system/sub/view/v/edit/id/<?php echo $system['id'];?>" title="edit the Systems">
        <img src="/images/edit.png" border="0"></a>
    </td>
    <?php } if(isAllow('admin_systems','read')){ ?>
    <td class="thc" align="center">
        <a href="/zfentry.php/panel/system/sub/view/id/<?php echo $system['id'];?>" title="display the Systems">
        <img src="/images/view.gif" border="0"></a>
    </td>
    <?php } if(isAllow('admin_systems','delete')){ ?>
    <td class="thc" align="center">
        <a href="/zfentry.php/panel/system/sub/delete/id/<?php echo $system['id'];?>" title="delete the Systems, then no restore after deleted" onclick="return delok('Systems');">
        <img src="/images/del.png" border="0"></a>
    </td>
    <?php }?>
</tr>
<?php }?>
</table>
