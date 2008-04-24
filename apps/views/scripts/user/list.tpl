<div class="barleft">
<div class="barright">
<p><b>Administration: Users List</b>
</div>
</div>
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0" class="tbframe">
<tr align="center">
    <th>Last Name</th>
    <th>First Name</th>
    <th>Office Phone</th>
    <th>Mobile Phoneh</th>
    <th>Email</th>
    <th>Role</th>
    <th>Username</th>
    <?php if(isAllow('admin_users','update')){
              echo'<th>Edit</td>';
          } 
          if(isAllow('admin_users','read')){
              echo'<th>View</td>';
          }
          if(isAllow('admin_users','delete')){
              echo'<th>Del</td>';
          }
    ?>
</tr>
<?php foreach($this->user_list as $user){ ?>
<tr>
    <td class="tdc">&nbsp;<?php echo $user['lastname'];?></td>
    <td class="tdc">&nbsp;<?php echo $user['firstname'];?></td>
    <td class="tdc">&nbsp;<?php echo $user['officephone'];?></td>
    <td class="tdc">&nbsp;<?php echo $user['mobile'];?></td>
    <td class="tdc">&nbsp;<?php echo $user['email'];?></td>
    <td class="tdc">&nbsp;<?php echo $user['rolename'];?></td>
    <td class="tdc">&nbsp;<?php echo $user['username'];?></td>
    <?php if(isAllow('admin_users','update')){ ?>
    <td class="thc" align="center">
        <a href="/zfentry.php/panel/user/sub/edit/v/edit/id/<?php echo $user['id'];?>" title="edit the Users">
        <img src="/images/edit.png" border="0"></a>
    </td>
    <?php } if(isAllow('admin_users','read')){ ?>
    <td class="thc" align="center">
        <a href="/zfentry.php/panel/user/sub/view/id/<?php echo $user['id'];?>" title="display the Users">
        <img src="/images/view.gif" border="0"></a>
    </td>
    <?php } if(isAllow('admin_users','delete')){ ?>
    <td class="thc" align="center">
        <a href="/zfentry.php/panel/user/sub/delete/id/<?php echo $user['id'];?>" title="delete the Users, then no restore after deleted" onclick="return delok('Users');">
        <img src="/images/del.png" border="0"></a>
    </td>
    <?php }?>
</tr>
<?php }?>
</table>
