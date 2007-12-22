<table>

	<form name='system_update' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='system_id' value='{$system_id}'>
		
	<tr><td>Name</td>    <td> <input type='text' name='system_name' value='{$system_name}'></td></tr>
	<tr><td>Nick Name</td><td> <input type='text' name='system_nickname' value='{$system_nickname}'></td></tr>
	<tr><td>Desc</td>     <td> <textarea name='system_desc' rows="5" cols="40">{$system_desc}</textarea> </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Type</td>  <td>{html_options values=$system_type_list output=$system_type_list name='system_type' selected=$system_type}	</td></tr>
	<tr><td>Primary Office</td>  <td> <input type='text' name='system_primary_office' value='{$system_primary_office}'>    </td></tr>

	<tr><td colspan='2'><br></td></tr>

	<tr><td>Confidentiality</td>  <td>{html_options values=$level_list output=$level_list name='system_confidentiality' selected=$system_confidentiality}</td></tr>

	<tr><td>Integrity</td>     <td> {html_options values=$level_list output=$level_list name='system_integrity' selected=$system_integrity}	</td></tr>

	<tr><td>Availability</td>  <td>{html_options values=$level_list output=$level_list name='system_availability' selected=$system_availability}</td></tr>	

	<tr><td>Tier</td>    <td>  <input type='text' name='system_tier' value='{$system_tier}'> </td></tr>
	<tr><td>Criticality Justification</td>       <td> <textarea name='system_criticality_justification' rows="5" cols="40">{$system_criticality_justification}</textarea></td></tr>
	<tr><td>Sensitivity Justification</td>       <td> <textarea name='system_sensitivity_justification' rows="5" cols="40">{$system_sensitivity_justification}</textarea></td></tr>
	
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='system'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Update' src='images/button_update.png'>
		</td>
	</tr>
	</form>

</table>