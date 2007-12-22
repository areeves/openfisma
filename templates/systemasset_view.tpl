<table>

	<tr><td>System ID</td>    <td> {$system_id}      </td></tr>
	<tr><td>Asset ID</td><td> {$asset_id}     </td></tr>
	<tr><td>System Is Owner</td>     <td> {$system_is_owner}       </td></tr>
	
</table>

<table>

	<tr>

		<td>
			<form name='systemasset_list' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='systemasset'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='systemasset_id'     value='{$systemasset_id}'>
				<input type='image'  name='form_action' value='Cancel' src='images/button_cancel.png'>
			</form>
		
		</td>
		
		<td>
			
			<form name='systemasset_update' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='systemasset'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='systemasset_id'     value='{$systemasset_id}'>
				<input type='image'  name='form_action' value='Update' src='images/button_update.png'>
			</form>
			
		</td>
	
		<td>
			<form name='systemasset_delete' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='systemasset'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='systemasset_id'     value='{$systemasset_id}'>
				<input type='image'  name='form_action' value='Delete' src='images/button_delete.png'>
			</form>
		
		</td>
		
	</tr>
	
</table>
