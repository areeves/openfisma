<table>

	<form name='role_create' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
	<tr><td>Name</td>    <td> <input type='text' name='role_name' value=''> </td></tr>
	<tr><td>Nick Name</td>     <td> <input type='text' name='role_nickname' value=''></td></tr>
	<tr><td>Desc</td>     <td> <textarea name='role_desc' rows="5" cols="40"></textarea>  </td></tr>
	<tr><td align='right' colspan='2'>
	    <input type='hidden' name='form_target' value='role'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Create' src='images/button_create.png'>
	</td></tr>
	</form>

</table>