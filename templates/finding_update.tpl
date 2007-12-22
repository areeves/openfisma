<table>

	<form name='finding_update' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='id'       value='{$id}'>

    <tr><td>Finding ID</td> <td> {$id} </td></tr>
	<tr><td>Source ID</td>  <td> {html_options options=$source_list name='source_id' selected=$source_id}  </td></tr>
	<tr><td>Asset ID</td>   <td> {html_options options=$asset_list name='asset_id' selected=$asset_id} </td></tr>
	<tr><td>Status</td>     <td> {html_options values=$finding_status_list output=$finding_status_list selected=$finding_status name='finding_status'}</td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Date Created</td>    <td> {html_select_date prefix="finding_date_created_" time=$finding_date_created start_year="-5" end_year="+1"}{html_select_time prefix="finding_date_created_" time=$finding_date_created} </td></tr>
	<tr><td>Date Discovered</td> <td> {html_select_date prefix="finding_date_discovered_" time=$finding_date_discovered start_year="-5" end_year="+1"}  </td></tr>
	<tr><td>Date Closed</td>     <td> {html_select_date prefix="finding_date_closed_" time=$finding_date_closed start_year="-5" end_year="+1"}{html_select_time prefix="finding_date_closed_" time=$finding_date_closed}  </td></tr>
	<tr><td>Data</td>            <td> <textarea name='finding_data' rows="5" cols="40">{$finding_data}</textarea>       </td></tr>
		
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='finding'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Update' src='images/button_update.png'>
		</td>
	</tr>
	</form>

</table>