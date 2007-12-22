<table>

	<tr><td>Source Name</td>  <td> {$source_name}    </td></tr>
	<tr><td>Nick Name</td>    <td> {$source_nickname} </td></tr>
	<tr><td>Source Desc</td>     <td> {$source_desc}       </td></tr>
	
</table>

<table>

	<tr>

		<td>
			<form name='findingsource_list' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='findingsource'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='source_id'     value='{$source_id}'>
				<input type='image'  name='form_action' value='Cancel' src='images/button_cancel.png'>
			</form>
		
		</td>
		
		<td>
			
			<form name='findingsource_update' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='findingsource'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='source_id'     value='{$source_id}'>
				<input type='image'  name='form_action' value='Update' src='images/button_update.png'>
			</form>
			
		</td>
	
		<td>
			<form name='findingsource_delete' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='findingsource'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='source_id'     value='{$source_id}'>
				<input type='image'  name='form_action' value='Delete' src='images/button_delete.png'>
			</form>
		
		</td>
		
	</tr>
	
</table>
