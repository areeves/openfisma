<table>

	<form name='' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>

    <tr><td>Type</td> <td> <input type='text' name='vuln_type'></td></tr>
	<tr><td>Desc Primary</td>    <td> <textarea name='vuln_desc_primary' rows="5" cols="40"></textarea> </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Desc Secondary</td>    <td> <textarea name='vuln_desc_secondary' rows="5" cols="40"></textarea></td></tr>
	<tr><td>Date Discovered</td>    <td>{html_select_date prefix="vuln_date_discovered_" time=$time start_year="-5" end_year="+1"}</td></tr>
	<tr><td>Date Modified</td>    <td>{html_select_date prefix="vuln_date_modified_" time=$time start_year="-5" end_year="+1"}</td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Date Published</td>    <td>{html_select_date prefix="vuln_date_published_" time=$time start_year="-5" end_year="+1"}</td></tr>
	<tr><td>Severity</td>    <td><input type='text' name='vuln_severity' value='0'></td></tr>
	<tr><td>Loss Availability</td>    <td><input type='text' name='vuln_loss_availability' value='0'></td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Loss Confidentiality</td>    <td><input type='text' name='vuln_loss_confidentiality' value='0'></td></tr>
	<tr><td>Loss Integrity</td>    <td><input type='text' name='vuln_loss_integrity' value='0'></td></tr>
	<tr><td>Loss Security Admin</td>    <td><input type='text' name='vuln_loss_security_admin' value='0'></td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Loss Security User</td>    <td><input type='text' name='vuln_loss_security_user' value='0'></td></tr>
	<tr><td>Loss Security Other</td>    <td><input type='text' name='vuln_loss_security_other' value='0'></td></tr>
	<tr><td>Type Access</td>    <td><input type='text' name='vuln_type_access' value='0'></td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Type Input</td>    <td><input type='text' name='vuln_type_input' value='0'></td></tr>
	<tr><td>Type Input Bound</td>    <td><input type='text' name='vuln_type_input_bound' value='0'></td></tr>
	<tr><td>Type Input Buffer</td>    <td><input type='text' name='vuln_type_input_buffer' value='0'></td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Type Design</td>    <td><input type='text' name='vuln_type_design' value='0'></td></tr>
	<tr><td>Type Exception</td>    <td><input type='text' name='vuln_type_exception' value='0'></td></tr>
	<tr><td>Type Environment</td>    <td><input type='text' name='vuln_type_environment' value='0'></td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Type Config</td>    <td><input type='text' name='vuln_type_config' value='0'></td></tr>
	<tr><td>Type Race</td>    <td><input type='text' name='vuln_type_race' value='0'></td></tr>
	<tr><td>Type Other</td>    <td><input type='text' name='vuln_type_other' value='0'></td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Range Local</td>    <td><input type='text' name='vuln_range_local' value='0'></td></tr>
	<tr><td>Range Remote</td>    <td><input type='text' name='vuln_range_remote' value='0'></td></tr>
	<tr><td>Range User</td>    <td><input type='text' name='vuln_range_user' value='0'></td></tr>
		
	    <input type='hidden' name='form_target' value='vuln'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Create' src='images/button_create.png'>
	</td></tr>
	</form>

</table>