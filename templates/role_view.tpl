<table>

	<tr><td>Name</td>    <td> {$role_name}      </td></tr>
	<tr><td>Nick Name</td>     <td> {$role_nickname}       </td></tr>
	<tr><td>Desc</td>     <td> {$role_desc}       </td></tr>
	
</table>

<table>

	<tr>

		<td>
			<form name='role_list' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='role'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='role_id'     value='{$role_id}'>
				<input type='image'  name='form_action' value='Cancel' src='images/button_cancel.png'>
			</form>
		
		</td>
		
		<td>
			
			<form name='role_update' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='role'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='role_id'     value='{$role_id}'>
				<input type='image'  name='form_action' value='Update' src='images/button_update.png'>
			</form>
			
		</td>
	
		<td>
			<form name='role_delete' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='role'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='role_id'     value='{$role_id}'>
				<input type='image'  name='form_action' value='Delete' src='images/button_delete.png'>
			</form>
		
		</td>
		
	</tr>
	
</table>
