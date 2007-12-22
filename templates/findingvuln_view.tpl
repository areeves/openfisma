<table>

	<tr><td>Finding ID</td>    <td> {$finding_id}      </td></tr>
	<tr><td>Vuln Seq</td>      <td> {$vuln_seq}     </td></tr>
	<tr><td>Vuln Type</td>     <td> {$vuln_type}       </td></tr>
	
</table>

<table>

	<tr>

		<td>
			<form name='findingvuln_list' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='findingvuln'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='findingvuln_id'     value='{$findingvuln_id}'>
				<input type='image'  name='form_action' value='Cancel' src='images/button_cancel.png'>
			</form>
		
		</td>
		
		<td>
			
			<form name='findingvuln_update' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='findingvuln'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='findingvuln_id'     value='{$findingvuln_id}'>
				<input type='image'  name='form_action' value='Update' src='images/button_update.png'>
			</form>
			
		</td>
	
		<td>
			<form name='findingvuln_delete' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='findingvuln'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='findingvuln_id'     value='{$findingvuln_id}'>
				<input type='image'  name='form_action' value='Delete' src='images/button_delete.png'>
			</form>
		
		</td>
		
	</tr>
	
</table>
