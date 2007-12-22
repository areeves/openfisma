<table>

	<form name='findingvuln_delete' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='findingvuln_id' value='{$findingvuln_id}'>
	<tr><td>Finding ID</td>    <td> {$finding_id}      </td></tr>
	<tr><td>Vuln Seq</td>      <td> {$vuln_seq}     </td></tr>
	<tr><td>Vuln Type</td>     <td> {$vuln_type}       </td></tr>
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='findingvuln'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Delete' src='images/button_delete.png'>
		</td>
	</tr>
	</form>

</table>