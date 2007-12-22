<table>

	<form name='findingvuln_update' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='findingvuln_id' value='{$findingvuln_id}'>
	<tr><td>Finding ID</td>    <td>    <input type='text'     name='finding_id' value='{$finding_id}'>   </td></tr>
	<tr><td>Vuln Seq</td>      <td>    <input type='text'     name='vuln_seq' value='{$vuln_seq}'>  </td></tr>
	<tr><td>Vuln Type</td>     <td>    <input type='text'     name='vuln_type' value='{$vuln_type}'>    </td></tr>
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='findingvuln'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Update' src='images/button_update.png'>
		</td>
	</tr>
	</form>

</table>