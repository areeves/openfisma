<table>

	<form name='findingsource_delete' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='source_id' value='{$source_id}'>
	<tr><td>Source Name</td>  <td> {$source_name}    </td></tr>
	<tr><td>Nick Name</td>    <td> {$source_nickname} </td></tr>
	<tr><td>Source Desc</td>     <td> {$source_desc}  </td></tr>
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='findingsource'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Delete' src='images/button_delete.png'>
		</td>
	</tr>
	</form>

</table>