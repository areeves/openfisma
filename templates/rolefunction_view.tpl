<table>

	<tr><td>Role ID</td>    <td> {$role_id}      </td></tr>
	<tr><td>Function ID</td><td> {$function_id}     </td></tr>
	
</table>

<table>

	<tr>

		<td>
			<form name='rolefunction_list' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='rolefunction'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='role_func_id'     value='{$role_func_id}'>
				<input type='image'  name='form_action' value='Cancel' src='images/button_cancel.png'>
			</form>
		
		</td>
		
		<td>
			
			<form name='rolefunction_update' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='rolefunction'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='role_func_id'     value='{$role_func_id}'>
				<input type='image'  name='form_action' value='Update' src='images/button_update.png'>
			</form>
			
		</td>
	
		<td>
			<form name='rolefunction_delete' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='rolefunction'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='role_func_id'     value='{$role_func_id}'>
				<input type='image'  name='form_action' value='Delete' src='images/button_delete.png'>
			</form>
		
		</td>
		
	</tr>
	
</table>
