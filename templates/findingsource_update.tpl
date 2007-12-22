<table>

	<form name='findingsource_update' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='source_id' value='{$source_id}'>
	<tr><td>Source Name</td>  <td> <input type='text' name='source_name' value='{$source_name}'>  </td></tr>
	<tr><td>Nick Name</td>    <td> <input type='text' name='source_nickname' value='{$source_nickname}'> </td></tr>
	<tr><td>Source Desc</td>     <td>  <textarea rows="5" cols="40" name='source_desc'>{$source_desc}</textarea>    </td></tr>
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='findingsource'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Update' src='images/button_update.png'>
		</td>
	</tr>
	</form>

</table>