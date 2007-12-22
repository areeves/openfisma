<table>

	<form name='rolefunction_delete' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='role_func_id' value='{$role_func_id}'>
	<tr><td>Role ID</td>    <td> {$role_id}      </td></tr>
	<tr><td>Function ID</td><td> {$function_id}     </td></tr>
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='rolefunction'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Delete' src='images/button_delete.png'>
		</td>
	</tr>
	</form>

</table>