<table>

	<form name='role_update' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='role_id' value='{$role_id}'>
	<tr><td>Name</td>    <td> <input type='text' name='role_name' value='{$role_name}'> </td></tr>
	<tr><td>Nick Name</td>     <td> <input type='text' name='role_nickname' value='{$role_nickname}'></td></tr>
	<tr><td>Desc</td>     <td> <textarea name='role_desc' rows="5" cols="40">{$role_desc}</textarea>  </td></tr>
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='role'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Update' src='images/button_update.png'>
		</td>
	</tr>
	</form>

</table>