<table>

	<form name='poam_create' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		
	<tr><td>Finding ID</td>    <td> {html_options options=$finding_list name='finding_id'}  </td></tr>
	<tr><td>Legacy Poam Id</td>    <td> <input type='text' name='legacy_poam_id' value='0'>      </td></tr>
	<tr><td>Is Repeat</td>    <td> <input type='text' name='poam_is_repeat' value='0'>      </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Previous Audits</td>    <td>  <textarea name='poam_previous_audits' rows="5" cols="40"></textarea>      </td></tr>
	<tr><td>Type</td>    <td> {html_options values=$poam_type_list output=$poam_type_list name='poam_type' selected=$poam_type}</td></tr>
	<tr><td>Status</td>    <td>{html_options values=$poam_status_list output=$poam_status_list name='poam_status' selected=$poam_status}</td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Blscr</td>    <td> {html_options options=$blscr_list name='poam_blscr'}      </td></tr>
	<tr><td>Created By</td>    <td> {html_options options=$user_list name='poam_created_by'}    </td></tr>
	<tr><td>Modified By</td>    <td> {html_options options=$user_list name='poam_modified_by'} </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Closed By</td>    <td>  {html_options options=$user_list name='poam_closed_by'}    </td></tr>
	<tr><td>Date Created</td>    <td> {html_select_date prefix="poam_date_created_" time=$time start_year="-5" end_year="+1"}{html_select_time prefix="poam_date_created_" time=$time} </td></tr>
	<tr><td>Date Modified</td>    <td> {html_select_date prefix="poam_date_modified_" time=$time start_year="-5" end_year="+1"}{html_select_time prefix="poam_date_modified_" time=$time}</td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Date Closed</td>    <td> {html_select_date prefix="poam_date_closed_" time=$time start_year="-5" end_year="+1"}{html_select_time prefix="poam_date_closed_" time=$time}</td></tr>
	<tr><td>Action Owner</td>    <td>  {html_options options=$user_list name='poam_action_owner'}    </td></tr>
	<tr><td>Action Suggested</td>    <td> <textarea name='poam_action_suggested' rows="5" cols="40"></textarea>       </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Action Planned</td>    <td> <textarea name='poam_action_suggested' rows="5" cols="40"></textarea>      </td></tr>
	<tr><td>Action Status</td>    <td> {html_options values=$poam_action_status_list output=$poam_action_status_list selected=$poam_action_status name='poam_action_status'}</td></tr>
	<tr><td>Action Approved By</td>    <td> {html_options options=$user_list name='poam_action_approved_by'}    </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Cmeasure</td>    <td> <textarea name='poam_cmeasure' rows="5" cols="40"></textarea>      </td></tr>
	<tr><td>Cmeasure Effectiveness</td>    <td>{html_options values=$level_list output=$level_list name='poam_cmeasure_effectiveness' selected=$poam_cmeasure_effectiveness}</td></tr>
	<tr><td>Cmeasure Justification</td>    <td> <textarea name='poam_cmeasure_justification' rows="5" cols="40"></textarea>      </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Action Resources</td>    <td> <textarea name='poam_cmeasure_justification' rows="5" cols="40"></textarea></td></tr>
	<tr><td>Action Date EST</td>    <td> {html_select_date prefix="poam_action_date_est_" time=$time start_year="-5" end_year="+1"}{html_select_time prefix="poam_action_date_est_" time=$time} </td></tr>
	<tr><td>Action Date Actual</td>    <td> {html_select_date prefix="poam_action_date_actual_" time=$time start_year="-5" end_year="+1"}{html_select_time prefix="poam_action_date_actual_" time=$time} </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Threat Source</td>    <td> <textarea name='poam_threat_source' rows="5" cols="40"></textarea>      </td></tr>
	<tr><td>Threat Level</td>    <td> {html_options values=$level_list output=$level_list name='poam_threat_level' selected=$poam_threat_level}</td></tr>
	<tr><td>Threat Justification</td>    <td> <textarea name='poam_threat_justification' rows="5" cols="40"></textarea>      </td></tr>
	
	<tr><td align='right' colspan='2'>
	    <input type='hidden' name='form_target' value='poam'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Create' src='images/button_create.png'>
	</td></tr>
	</form>

</table>