<table>

	<form name='usergroup_delete' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='user_group_id' value='{$user_group_id}'>
	<tr><td>User ID</td>    <td> {$user_id}      </td></tr>
	<tr><td>System Group ID</td><td> {$sysgroup_id}     </td></tr>
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='usergroup'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Delete' src='images/button_delete.png'>
		</td>
	</tr>
	</form>

</table>