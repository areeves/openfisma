<table>

	<form name='vuln_update' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='vuln_seq' value='{$vuln_seq}'>

	<tr><td>Type</td> <td> <input type='text' name='vuln_type' value='{$vuln_type}'></td></tr>
	<tr><td>Desc Primary</td>    <td> <textarea name='vuln_desc_primary' rows="5" cols="40">{$vuln_desc_primary}</textarea> </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Desc Secondary</td>    <td> <textarea name='vuln_desc_secondary' rows="5" cols="40">{$vuln_desc_secondary}</textarea></td></tr>
	<tr><td>Date Discovered</td>    <td>{html_select_date prefix="vuln_date_discovered_" time=$vuln_date_discovered start_year="-5" end_year="+1"}</td></tr>
	<tr><td>Date Modified</td>    <td>{html_select_date prefix="vuln_date_modified_" time=$vuln_date_modified start_year="-5" end_year="+1"}</td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Date Published</td>    <td>{html_select_date prefix="vuln_date_published_" time=$vuln_date_published start_year="-5" end_year="+1"}</td></tr>
	<tr><td>Severity</td>    <td><input type='text' name='vuln_severity' value='{$vuln_severity}'> </td></tr>
	<tr><td>Loss Availability</td>    <td><input type='text' name='vuln_loss_availability' value='{$vuln_loss_availability}'></td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Loss Confidentiality</td>    <td><input type='text' name='vuln_loss_confidentiality' value='{$vuln_loss_confidentiality}'></td></tr>
	<tr><td>Loss Integrity</td>    <td><input type='text' name='vuln_loss_integrity' value='{$vuln_loss_integrity}'></td></tr>
	<tr><td>Loss Security Admin</td>    <td><input type='text' name='vuln_loss_security_admin' value='{$vuln_loss_security_admin}'></td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Loss Security User</td>    <td><input type='text' name='vuln_loss_security_user' value='{$vuln_loss_security_user}'></td></tr>
	<tr><td>Loss Security Other</td>    <td><input type='text' name='vuln_loss_security_other' value='{$vuln_loss_security_other}'></td></tr>
	<tr><td>Type Access</td>    <td><input type='text' name='vuln_type_access' value='{$vuln_type_access}'></td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Type Input</td>    <td><input type='text' name='vuln_type_input' value='{$vuln_type_input}'></td></tr>
	<tr><td>Type Input Bound</td>    <td><input type='text' name='vuln_type_input_bound' value='{$vuln_type_input_bound}'></td></tr>
	<tr><td>Type Input Buffer</td>    <td><input type='text' name='vuln_type_input_buffer' value='{$vuln_type_input_buffer}'></td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Type Design</td>    <td><input type='text' name='vuln_type_design' value='{$vuln_type_design}'></td></tr>
	<tr><td>Type Exception</td>    <td><input type='text' name='vuln_type_exception' value='{$vuln_type_exception}'></td></tr>
	<tr><td>Type Environment</td>    <td><input type='text' name='vuln_type_environment' value='{$vuln_type_environment}'></td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Type Config</td>    <td><input type='text' name='vuln_type_config' value='{$vuln_type_config}'></td></tr>
	<tr><td>Type Race</td>    <td><input type='text' name='vuln_type_race' value='{$vuln_type_race}'></td></tr>
	<tr><td>Type Other</td>    <td><input type='text' name='vuln_type_other' value='{$vuln_type_other}'></td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Range Local</td>    <td><input type='text' name='vuln_range_local' value='{$vuln_range_local}'></td></tr>
	<tr><td>Range Remote</td>    <td><input type='text' name='vuln_range_remote' value='{$vuln_range_remote}'></td></tr>
	<tr><td>Range User</td>    <td><input type='text' name='vuln_range_user' value='{$vuln_range_user}'></td></tr>

	<tr>	
	    <td>
	    <input type='hidden' name='form_target' value='vuln'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Update' src='images/button_update.png'>
		</td>
	</tr>
	</form>

</table>