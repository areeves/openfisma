<table>

	<form name='rolesysgroup_update' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='role_group_id' value='{$role_group_id}'>
	<tr><td>Role ID</td>    <td>  {html_options options=$role_list selected=$role_id name='role_id'}    </td></tr>
	<tr><td>System Group ID</td><td> {html_options options=$group_list selected=$sysgroup_id name='sysgroup_id'}     </td></tr>
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='rolesysgroup'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Update' src='images/button_update.png'>
		</td>
	</tr>
	</form>

</table>