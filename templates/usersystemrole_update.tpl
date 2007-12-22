<table>

	<form name='usersystemrole_update' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='usersystemrole_id' value='{$usersystemrole_id}'>
	<tr><td>User ID</td>    <td>  <input type='text'     name='user_id'         value='{$user_id}'>   </td></tr>
	<tr><td>System Group ID</td><td> <input type='text'     name='sysgroup_id'         value='{$sysgroup_id}'>     </td></tr>
	<tr><td>Role ID</td>     <td> <input type='text'     name='role_id'         value='{$role_id}'>       </td></tr>
	<tr><td>System ID</td>         <td> <input type='text'     name='system_id'         value='{$system_id}'>           </td></tr>
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='usersystemrole'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Update' src='images/button_update.png'>
		</td>
	</tr>
	</form>

</table>