<table>

	<tr><td>Name</td>    <td> {$network_name}      </td></tr>
	<tr><td>Nick Name</td><td> {$network_nickname}     </td></tr>
	<tr><td>Desc</td>     <td> {$network_desc}       </td></tr>
	
</table>

<table>

	<tr>

		<td>
			<form name='network_list' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='network'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='network_id'     value='{$network_id}'>
				<input type='image'  name='form_action' value='Cancel' src='images/button_cancel.png'>
			</form>
		
		</td>
		
		<td>
			
			<form name='network_update' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='network'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='network_id'     value='{$network_id}'>
				<input type='image'  name='form_action' value='Update' src='images/button_update.png'>
			</form>
			
		</td>
	
		<td>
			<form name='network_delete' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='network'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='network_id'     value='{$network_id}'>
				<input type='image'  name='form_action' value='Delete' src='images/button_delete.png'>
			</form>
		
		</td>
		
	</tr>
	
</table>
