<table>

	<tr><td>User ID</td>    <td> {$user_id}      </td></tr>
	<tr><td>System Group ID</td><td> {$sysgroup_id}     </td></tr>
	<tr><td>Role ID</td>     <td> {$role_id}       </td></tr>
	<tr><td>System ID</td>         <td> {$system_id}           </td></tr>
	
</table>

<table>

	<tr>

		<td>
			<form name='usersystemrole_list' method='post' action='usersystemrole_list.php'>
        	    <input type='hidden' name='form_target' value='usersystemrole'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='usersystemrole_id'     value='{$usersystemrole_id}'>
				<input type='image'  name='form_action' value='Cancel' src='images/button_cancel.png'>
			</form>
		
		</td>
		
		<td>
			
			<form name='usersystemrole_update' method='post' action='usersystemrole_update.php'>
        	    <input type='hidden' name='form_target' value='usersystemrole'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='usersystemrole_id'     value='{$usersystemrole_id}'>
				<input type='image'  name='form_action' value='Update' src='images/button_update.png'>
			</form>
			
		</td>
	
		<td>
			<form name='usersystemrole_delete' method='post' action='usersystemrole_delete.php'>
        	    <input type='hidden' name='form_target' value='usersystemrole'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='usersystemrole_id'     value='{$usersystemrole_id}'>
				<input type='image'  name='form_action' value='Delete' src='images/button_delete.png'>
			</form>
		
		</td>
		
	</tr>
	
</table>
