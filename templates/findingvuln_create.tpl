<table>

	<form name='findingvuln_create' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
	<tr><td>Finding ID</td>    <td>    <input type='text'     name='finding_id'>   </td></tr>
	<tr><td>Vuln Seq</td>      <td>    <input type='text'     name='vuln_seq'>  </td></tr>
	<tr><td>Vuln Type</td>     <td>    <input type='text'     name='vuln_type'>    </td></tr>
	<tr><td align='right' colspan='2'>
	    <input type='hidden' name='form_target' value='findingvuln'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Create' src='images/button_create.png'>
	</td></tr>
	</form>

</table>