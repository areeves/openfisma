<table>

	<tr><td>Name</td>    <td> {$plugin_name}      </td></tr>
	<tr><td>Nick Name</td><td> {$plugin_nickname}     </td></tr>
	<tr><td>Abbreviation</td>     <td> {$plugin_abbreviation}       </td></tr>
	<tr><td>Desc</td>       <td> {$plugin_desc}       </td></tr>
	
</table>

<table>

	<tr>

		<td>
			<form name='plugin_list' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='plugin'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='plugin_id'     value='{$plugin_id}'>
				<input type='image'  name='form_action' value='Cancel' src='images/button_cancel.png'>
			</form>
		
		</td>
		
		<td>
			
			<form name='plugin_update' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='plugin'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='plugin_id'     value='{$plugin_id}'>
				<input type='image'  name='form_action' value='Update' src='images/button_update.png'>
			</form>
			
		</td>
	
		<td>
			<form name='plugin_delete' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='plugin'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='plugin_id'     value='{$plugin_id}'>
				<input type='image'  name='form_action' value='Delete' src='images/button_delete.png'>
			</form>
		
		</td>
		
	</tr>
	
</table>
