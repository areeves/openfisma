<table>

	<tr><td>System ID</td>    <td> {$system_id}      </td></tr>
	<tr><td>System Group ID</td><td> {$sysgroup_id}     </td></tr>
	
</table>

<table>

	<tr>

		<td>
			<form name='systemgroupsystem_list' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='systemgroupsystem'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='systemgroupsystem_id'     value='{$systemgroupsystem_id}'>
				<input type='image'  name='form_action' value='Cancel' src='images/button_cancel.png'>
			</form>
		
		</td>
		
		<td>
			
			<form name='systemgroupsystem_update' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='systemgroupsystem'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='systemgroupsystem_id'     value='{$systemgroupsystem_id}'>
				<input type='image'  name='form_action' value='Update' src='images/button_update.png'>
			</form>
			
		</td>
	
		<td>
			<form name='systemgroupsystem_delete' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='systemgroupsystem'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='systemgroupsystem_id'     value='{$systemgroupsystem_id}'>
				<input type='image'  name='form_action' value='Delete' src='images/button_delete.png'>
			</form>
		
		</td>
		
	</tr>
	
</table>
