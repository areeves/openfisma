<table>

	<form name='systemgroup_update' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='sysgroup_id' value='{$sysgroup_id}'>
	<tr><td>Name</td>    <td> <input type='text'     name='sysgroup_name'   value='{$sysgroup_name}'>      </td></tr>
	<tr><td>Nick Name</td><td> <input type='text'     name='sysgroup_nickname'   value='{$sysgroup_nickname}'>     </td></tr>
	<tr><td>Is Identity</td>     <td> <input type='text'     name='sysgroup_is_identity'   value='{$sysgroup_is_identity}'>       </td></tr>
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='systemgroup'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Update' src='images/button_update.png'>
		</td>
	</tr>
	</form>

</table>