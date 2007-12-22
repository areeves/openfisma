<table>

	<form name='usersystemrole_create' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
	<tr><td>User ID</td>    <td>  <input type='text'     name='user_id'         value=''>   </td></tr>
	<tr><td>System Group ID</td><td> <input type='text'     name='sysgroup_id'         value=''>     </td></tr>
	<tr><td>Role ID</td>     <td> <input type='text'     name='role_id'         value=''>       </td></tr>
	<tr><td>System ID</td>         <td> <input type='text'     name='system_id'         value=''>           </td></tr>
	<tr><td align='right' colspan='2'>
	    <input type='hidden' name='form_target' value='usersystemrole'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Create' src='images/button_create.png'>
	</td></tr>
	</form>

</table>