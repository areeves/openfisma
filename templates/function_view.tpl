<table>

	<tr><td>Name</td>    <td> {$function_name}      </td></tr>
	<tr><td>Screen</td>     <td> {$function_screen}            </td></tr>
	<tr><td>Action</td>  <td> {$function_action}    </td></tr>
	<tr><td>Desc</td>    <td> {$function_desc} </td></tr>
	<tr><td>Open</td>       <td> {$function_open}       </td></tr>
	
</table>

<table>

	<tr>

		<td>
			<form name='function_list' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='function'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='function_id'     value='{$function_id}'>
				<input type='image'  name='form_action' value='Cancel' src='images/button_cancel.png'>
			</form>
		
		</td>
		
		<td>
			
			<form name='function_update' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='function'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='function_id'     value='{$function_id}'>
				<input type='image'  name='form_action' value='Update' src='images/button_update.png'>
			</form>
			
		</td>
	
		<td>
			<form name='function_delete' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='function'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='function_id'     value='{$function_id}'>
				<input type='image'  name='form_action' value='Delete' src='images/button_delete.png'>
			</form>
		
		</td>
		
	</tr>
	
</table>
