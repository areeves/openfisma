<table>

	<form name='rolefunction_update' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='role_func_id' value='{$role_func_id}'>
	<tr><td>Role ID</td>    <td>  {html_options options=$role_list selected=$role_id name='role_id'}    </td></tr>
	<tr><td>Function ID</td><td> {html_options options=$func_list selected=$function_id name='function_id'}     </td></tr>
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='rolefunction'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Update' src='images/button_update.png'>
		</td>
	</tr>
	</form>

</table>