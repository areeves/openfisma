<table>

	<tr><td>Name</td>    <td> {$sysgroup_name}      </td></tr>
	<tr><td>Nick Name</td><td> {$sysgroup_nickname}     </td></tr>
	<tr><td>Is Identity</td>     <td> {$sysgroup_is_identity}       </td></tr>
	
</table>

<table>

	<tr>

		<td>
			<form name='systemgroup_list' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='systemgroup'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='sysgroup_id'     value='{$sysgroup_id}'>
				<input type='image'  name='form_action' value='Cancel' src='images/button_cancel.png'>
			</form>
		
		</td>
		
		<td>
			
			<form name='systemgroup_update' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='systemgroup'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='sysgroup_id'     value='{$sysgroup_id}'>
				<input type='image'  name='form_action' value='Update' src='images/button_update.png'>
			</form>
			
		</td>
	
		<td>
			<form name='systemgroup_delete' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='systemgroup'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='sysgroup_id'     value='{$sysgroup_id}'>
				<input type='image'  name='form_action' value='Delete' src='images/button_delete.png'>
			</form>
		
		</td>
		
	</tr>
	
</table>
