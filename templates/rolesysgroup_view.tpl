<table>

	<tr><td>Role ID</td>    <td> {$role_id}      </td></tr>
	<tr><td>System Group ID</td><td> {$sysgroup_id}     </td></tr>
	
</table>

<table>

	<tr>

		<td>
			<form name='rolesysgroup_list' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='rolesysgroup'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='role_group_id'     value='{$role_group_id}'>
				<input type='image'  name='form_action' value='Cancel' src='images/button_cancel.png'>
			</form>
		
		</td>
		
		<td>
			
			<form name='rolesysgroup_update' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='rolesysgroup'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='role_group_id'     value='{$role_group_id}'>
				<input type='image'  name='form_action' value='Update' src='images/button_update.png'>
			</form>
			
		</td>
	
		<td>
			<form name='rolesysgroup_delete' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='rolesysgroup'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='role_group_id'     value='{$role_group_id}'>
				<input type='image'  name='form_action' value='Delete' src='images/button_delete.png'>
			</form>
		
		</td>
		
	</tr>
	
</table>
