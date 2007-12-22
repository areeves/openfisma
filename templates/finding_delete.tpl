<table>

	<form name='finding_delete' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='id'       value='{$id}'>

    <tr><td>Finding ID</td> <td> {$id}             </td></tr>
	<tr><td>Source ID</td>  <td> {$source_id}      </td></tr>
	<tr><td>Asset ID</td>   <td> {$asset_id}       </td></tr>
	<tr><td>Status</td>     <td> {$finding_status} </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Date Created</td>     <td> {$finding_date_created}    </td></tr>
	<tr><td>Date Discovered</td>  <td> {$finding_date_discovered} </td></tr>
	<tr><td>Date Closed</td>      <td> {$finding_date_closed}     </td></tr>
	<tr><td>Data</td>             <td> {$finding_data}            </td></tr>
	
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='finding'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Delete' src='images/button_delete.png'>
		</td>
	</tr>
	</form>

</table>