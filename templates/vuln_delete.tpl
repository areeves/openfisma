<table>

	<form name='vuln_delete' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer'    value='{$this_page}'>
		<input type='hidden' name='vuln_seq'     value='{$vuln_seq}'>
		
<!--    <tr><td>Seq</td>    <td> {$vuln_seq}      </td></tr>-->
	<tr><td>Type</td>    <td> {$vuln_type}      </td></tr>
	<tr><td>Desc Primary</td>    <td> {$vuln_desc_primary}      </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Desc Secondary</td>    <td> {$vuln_desc_secondary}      </td></tr>
	<tr><td>Date Discovered</td>    <td> {$vuln_date_discovered}      </td></tr>
	<tr><td>Date Modified</td>    <td> {$vuln_date_modified}      </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Date Published</td>    <td> {$vuln_date_published}      </td></tr>
	<tr><td>Severity</td>    <td> {$vuln_severity}      </td></tr>
	<tr><td>Loss Availability</td>    <td> {$vuln_loss_availability}      </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Loss Confidentiality</td>    <td> {$vuln_loss_confidentiality}      </td></tr>
	<tr><td>Loss Integrity</td>    <td> {$vuln_loss_integrity}      </td></tr>
	<tr><td>Loss Security Admin</td>    <td> {$vuln_loss_security_admin}      </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Loss Security User</td>    <td> {$vuln_loss_security_user}      </td></tr>
	<tr><td>Loss Security Other</td>    <td> {$vuln_loss_security_other}      </td></tr>
	<tr><td>Type Access</td>    <td> {$vuln_type_access}      </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Type Input</td>    <td> {$vuln_type_input}      </td></tr>
	<tr><td>Type Input Bound</td>    <td> {$vuln_type_input_bound}      </td></tr>
	<tr><td>Type Input Buffer</td>    <td> {$vuln_type_input_buffer}      </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Type Design</td>    <td> {$vuln_type_design}      </td></tr>
	<tr><td>Type Exception</td>    <td> {$vuln_type_exception}      </td></tr>
	<tr><td>Type Environment</td>    <td> {$vuln_type_environment}      </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Type Config</td>    <td> {$vuln_type_config}      </td></tr>
	<tr><td>Type Race</td>    <td> {$vuln_type_race}      </td></tr>
	<tr><td>Type Other</td>    <td> {$vuln_type_other}      </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Range Local</td>    <td> {$vuln_range_local}      </td></tr>
	<tr><td>Range Remote</td>    <td> {$vuln_range_remote}      </td></tr>
	<tr><td>Range User</td>    <td> {$vuln_range_user}      </td></tr>
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='vuln'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Delete' src='images/button_delete.png'>
		</td>
	</tr>
	</form>

</table>