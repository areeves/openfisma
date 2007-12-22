<table>

	<form name='poam_delete' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='poam_id' value='{$poam_id}'>

	<tr><td>Finding ID</td>    <td> {$finding_id}      </td></tr>
	<tr><td>Legacy Poam Id</td>    <td> {$legacy_poam_id}      </td></tr>
	<tr><td>Is Repeat</td>    <td> {$poam_is_repeat}      </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Previous Audits</td>    <td> {$poam_previous_audits}      </td></tr>
	<tr><td>Type</td>    <td> {$poam_type}      </td></tr>
	<tr><td>Status</td>    <td> {$poam_status}      </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Blscr</td>    <td> {$poam_blscr}      </td></tr>
	<tr><td>Created By</td>    <td> {$poam_created_by}      </td></tr>
	<tr><td>Modified By</td>    <td> {$poam_modified_by}      </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Closed By</td>    <td> {$poam_closed_by}      </td></tr>
	<tr><td>Date Created</td>    <td> {$poam_date_created}      </td></tr>
	<tr><td>Date Modified</td>    <td> {$poam_date_modified}      </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Date Closed</td>    <td> {$poam_date_closed}      </td></tr>
	<tr><td>Action Owner</td>    <td> {$poam_action_owner}      </td></tr>
	<tr><td>Action Suggested</td>    <td> {$poam_action_suggested}      </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Action Planned</td>    <td> {$poam_action_planned}      </td></tr>
	<tr><td>Action Status</td>    <td> {$poam_action_status}      </td></tr>
	<tr><td>Action Approved By</td>    <td> {$poam_action_approved_by}      </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Cmeasure</td>    <td> {$poam_cmeasure}      </td></tr>
	<tr><td>Cmeasure Effectiveness</td>    <td> {$poam_cmeasure_effectiveness}      </td></tr>
	<tr><td>Cmeasure Justification</td>    <td> {$poam_cmeasure_justification}      </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Action Resources</td>    <td> {$poam_action_resources}      </td></tr>
	<tr><td>Action Date EST</td>    <td> {$poam_action_date_est}      </td></tr>
	<tr><td>Action Date Actual</td>    <td> {$poam_action_date_actual}      </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Threat Source</td>    <td> {$poam_threat_source}      </td></tr>
	<tr><td>Threat Level</td>    <td> {$poam_threat_level}      </td></tr>
	<tr><td>Threat Justification</td>    <td> {$poam_threat_justification}      </td></tr>
		
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='poam'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Delete' src='images/button_delete.png'>
		</td>
	</tr>
	</form>

</table>