<table>

	<form name='usergroup_create' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
	<tr><td>User ID</td>    <td> {html_options options=$user_list selected=$user_id name='user_id'}  </td></tr>
	<tr><td>System Group ID</td><td> {html_options options=$group_list selected=$sysgroup_id name='sysgroup_id'}     </td></tr>
	<tr><td align='right' colspan='2'>
	    <input type='hidden' name='form_target' value='usergroup'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Create' src='images/button_create.png'>
	</td></tr>
	</form>

</table>