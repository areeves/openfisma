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
<p><b>Administration: System Groups List</b>
</div>
</div>
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0" class="tbframe">
<tr align="center">
    <th>System Group Name</th>
    <th>System Group Nickname</th>
    <?php if(isAllow('admin_sys_groups','update')){
              echo'<th>Edit</td>';
          } 
          if(isAllow('admin_sys_groups','read')){
              echo'<th>View</td>';
          }
          if(isAllow('admin_sys_groups','delete')){
              echo'<th>Del</td>';
          }
    ?>
</tr>
<?php foreach($this->sys_group_list as $sys_group){ ?>
<tr>
    <td class="tdc">&nbsp;<?php echo $sys_group['name'];?></td>
    <td class="tdc">&nbsp;<?php echo $sys_group['nickname'];?></td>
    <?php if(isAllow('admin_sys_groups','update')){ ?>
    <td class="thc" align="center">
        <a href="/zfentry.php/panel/sysgroup/sub/view/v/edit/id/<?php echo $sys_group['id'];?>" title="edit the System Groups">
        <img src="/images/edit.png" border="0"></a>
    </td>
    <?php } if(isAllow('admin_sys_groups','read')){ ?>
    <td class="thc" align="center">
        <a href="/zfentry.php/panel/sysgroup/sub/view/id/<?php echo $sys_group['id'];?>" title="display the System Groups">
        <img src="/images/view.gif" border="0"></a>
    </td>
    <?php } if(isAllow('admin_sys_groups','delete')){ ?>
    <td class="thc" align="center">
        <a href="/zfentry.php/panel/sysgroup/sub/delete/id/<?php echo $sys_group['id'];?>" title="delete the System Groups, then no restore after deleted" onclick="return delok('System Groups');">
        <img src="/images/del.png" border="0"></a>
    </td>
    <?php }?>
</tr>
<?php }?>
</table>
