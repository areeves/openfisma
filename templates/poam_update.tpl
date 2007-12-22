<table border='0'>

	<form name='poam_update' method='post' action='{$this_page}'>
	<input type='hidden' name='referrer' value='{$this_page}'>
	<input type='hidden' name='poam_id'  value='{$poam_id}'>

	<tr><td colspan='2'><hr></td></tr>

	<tr><td>Finding ID: {$finding_id} </td></tr>

	<tr>

		<td>Is Repeat

			{if $remediation_update_is_repeat eq '1'}
				{html_options options=$yesno_list name='poam_is_repeat' selected=$poam_is_repeat}
			{else}
				<input type='hidden' name='poam_is_repeat' value='{$poam_is_repeat}'>
				{$poam_is_repeat}
			{/if}

		</td>

		<td>Previous Audits 

			{if $remediation_update_previous_audits eq '1' and $poam_is_repeat eq '1'}
				<input type='text'   name='poam_previous_audits' value='{$poam_previous_audits}'>
			{else}
				<input type='hidden' name='poam_previous_audits' value='{$poam_previous_audits}'>
				{$poam_previous_audits}
			{/if}

		</td>

	</tr>

	<tr><td colspan='2'><hr></td></tr>

	<tr>
		<td>Remediation ID: {$poam_id} </td>

		<td>Legacy Poam Id:

			{if $remediation_update_legacy_poam_id eq '1'}
				<input type='text' name='legacy_poam_id' value='{$legacy_poam_id}'>
			{else}
				<input type='hidden' name='legacy_poam_id' value='{$legacy_poam_id}'>
				{$legacy_poam_id}
			{/if}

		</td>

	</tr>

	<tr>
		<td>Type: 

			{if $remediation_update_type eq '1' and $poam_status eq 'OPEN'}
				{html_options options=$poam_type_list name='poam_type' selected=$poam_type} 
			{else}
				<input type='hidden' name='poam_type' value='{$poam_type}'>
				{$poam_type}
			{/if}
		</td>

		<td>Status: {$poam_status}</td>

	</tr>

	<tr><td colspan='2'><hr></td></tr>

	<tr>
		<td>Responsible System</td>
		<td>

			{if $remediation_update_action_owner and $poam_status eq 'OPEN'}
				{html_options options=$system_list selected=$poam_action_owner name='poam_action_owner'}
			{else}
				<input type='hidden' name='poam_action_owner' value='{$poam_action_owner}'>
				{$poam_action_owner}
			{/if}

		</td>

	</tr>

	<tr valign='top'>
		<td>Recommendation</td>
		<td>

			{if $remediation_update_action_suggested eq '1' and $poam_status eq 'OPEN'}
				<textarea name='poam_action_suggested' rows="5" cols="40">{$poam_action_suggested}</textarea>
			{else}
				<input type='hidden' name='poam_action_suggested' value='{$poam_action_suggested}'>
				{$poam_action_suggested}
			{/if}

		</td>

	</tr>

	<tr valign='top'>
		<td>Course of Action </td>
		<td>

			{if $remediation_update_action_planned eq '1' and $poam_status eq 'OPEN'}
				<textarea name='poam_action_planned' rows="5" cols="40">{$poam_action_planned}</textarea>
			{else}
				<input type='hidden' name='poam_action_planned' value='{$poam_action_planned}'>
				{$poam_action_planned}
			{/if}

		</td>

	</tr>

	<tr valign='top'>
		<td>Resources Required</td>
		<td>

			{if $remediation_update_action_resources eq '1' and $poam_status eq 'OPEN'}
				<textarea name='poam_action_resources' rows="5" cols="40">{$poam_action_resources}</textarea>
			{else}
				<input type='hidden' name='poam_action_resources' value='{$poam_action_resources}'>
				{$poam_action_resources}
			{/if}

		</td>

	</tr>

	<tr><td colspan='2'><hr></td></tr>

	<tr>
		<td>Estimated Completion Date</td>
		<td>

			{if $remediation_update_action_date_est eq '1' and $poam_status eq 'OPEN'}
				{html_select_date prefix="poam_action_date_est_" time=$poam_action_date_est start_year="0" end_year="+1"}
			{else}
				<input type='hidden' name='poam_action_date_est' value='{$poam_action_date_est}'>
				{$poam_action_date_est}
			{/if}

		</td>

	</tr>

	<tr>
		<td>Date Evidence Submitted</td>
		<td>{$poam_action_date_actual} </td>

	</tr>

	<tr><td colspan='2'><hr></td></tr>

	<tr valign='top'>

		<td>Threat Source</td>
        <td>

			{if $remediation_update_threat_source eq '1' and $poam_status eq 'OPEN'}
				<textarea name='poam_threat_source' rows="5" cols="40">{$poam_threat_source}</textarea>
			{else}
				<input type='hidden' name='poam_threat_source' value='{$poam_threat_source}'>
				{$poam_threat_source}
			{/if}

		</td>

	</tr>

	<tr>

		<td>Threat Level </td>
		<td>

			{if $remediation_update_threat_level eq '1' and $poam_status eq 'OPEN'}
				{html_options values=$level_list output=$level_list name='poam_threat_level' selected=$poam_threat_level}
			{else}
				<input type='hidden' name='poam_threat_level' value='{$poam_threat_level}'>
				{$poam_threat_level}
			{/if}

		</td>

	</tr>

	<tr valign='top'>

		<td>Justification for Threat Level</td>
		<td>

			{if $remediation_update_threat_justification eq '1' and $poam_status eq 'OPEN'}
				<textarea name='poam_threat_justification' rows="5" cols="40">{$poam_threat_justification}</textarea>
			{else}
				<input type='hidden' name='poam_threat_justification' value='{$poam_threat_justification}'>
				{$poam_threat_justification}
			{/if}

		</td>

	</tr>

	<tr><td colspan='2'><hr></td></tr>

	<tr valign='top'>

		<td>Countermeasure(s)</td>
		<td>

			{if $remediation_update_cmeasure eq '1' and $poam_status eq 'OPEN'}
				<textarea name='poam_cmeasure' rows="5" cols="40">{$poam_cmeasure}</textarea>
			{else}
				<input type='hidden' name='poam_cmeasure' value='{$poam_cmeasure}'>
				{$poam_cmeasure}
			{/if}

		</td>

	</tr>

	<tr valign='top'>

		<td>Countermeasure Effectiveness</td>    
		<td>

			{if $remediation_update_cmeasure_effectiveness eq '1' and $poam_status eq 'OPEN'}
				{html_options values=$level_list output=$level_list name='poam_cmeasure_effectiveness' selected=$poam_cmeasure_effectiveness}
			{else}
				<input type='hidden' name='poam_cmeasure_effectiveness' value='{$poam_cmeasure_effectiveness}'>
				{$poam_cmeasure_effectiveness}
			{/if}

		</td>

	</tr>

	<tr valign='top'>

		<td>Justification for Effectiveness</td>
		<td>

			{if $remediation_update_cmeasure_justification eq '1' and $poam_status eq 'OPEN'}
				<textarea name='poam_cmeasure_justification' rows="5" cols="40">{$poam_cmeasure_justification}</textarea>
			{else}
				<input type='hidden' name='poam_cmeasure_justification' value='{$poam_cmeasure_justification}'>
				{$poam_cmeasure_justification}
			{/if}

		</td>

	</tr>

	<tr><td colspan='2'><hr></td></tr>

	<tr>
		<td>SSO Approval 

			{if $remediation_update_action_status eq '1'}

				{if $poam_status eq 'OPEN' or $poam_status eq 'EN'}
					{html_options options=$approval_list selected=$poam_action_status name='poam_action_status'}
				{else}
					<input type='hidden' name='poam_action_status' value='{$poam_action_status}'>
					{$poam_action_status}
				{/if}

			{else}
				<input type='hidden' name='poam_action_status' value='{$poam_action_status}'>
				{$poam_action_status}
			{/if}

		</td>

		<td>Approved By: {$poam_action_approved_by} </td>

	</tr>

	<tr><td colspan='2'><hr></td></tr>
	
	<tr>
		<td colspan='2'>

			{if $show_cancel eq '1'}
				<input type='hidden' name='form_target' value='remediation'>
				<input type='image'  name='form_action' value='Cancel' src='images/button_cancel.png'>
			{/if}

			{if $show_update eq '1'}
				<input type='hidden' name='form_target' value='remediation'>
				<input type='image' name='form_action' value='update' src='images/button_update.png'>
			{/if}

			{if $show_delete eq '1'}
				<input type='hidden' name='form_target' value='remediation'>
				<input type='image' name='form_action' value='Delete' src='images/button_delete.png'>
			{/if}

		</td>
	</tr>
	</form>

</table>

{* TEMPORARILY REMOVED UNTIL A BETTER PLACE CAN BE FOUND FOR IT
<table>

	<tr><td>Date Opened: {$poam_date_created} by {$poam_created_by} </td></tr>
	<tr><td>Date Updated: {$poam_date_modified} by {$poam_modified_by} </td></tr>
	<tr><td>Date Closed: {$poam_date_closed}</td></tr>

	<tr><td colspan='2'><hr></td></tr>


</table>
*}