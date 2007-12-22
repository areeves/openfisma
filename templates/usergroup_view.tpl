<table>

	<tr><td>User ID</td>    <td> {$user_id}      </td></tr>
	<tr><td>System Group ID</td><td> {$sysgroup_id}     </td></tr>

</table>

<table>

	<tr>

		<td>
			<form name='usergroup_list' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='usergroup'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='user_group_id'     value='{$user_group_id}'>
				<input type='image'  name='form_action' value='Cancel' src='images/button_cancel.png'>
			</form>
		
		</td>
		
		<td>
			
			<form name='usergroup_update' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='usergroup'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='user_group_id'     value='{$user_group_id}'>
				<input type='image'  name='form_action' value='Update' src='images/button_update.png'>
			</form>
			
		</td>
	
		<td>
			<form name='usergroup_delete' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='usergroup'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='user_group_id'     value='{$user_group_id}'>
				<input type='image'  name='form_action' value='Delete' src='images/button_delete.png'>
			</form>
		
		</td>
		
	</tr>
	
</table>
