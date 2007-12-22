<table>

	<form name='findingsource_create' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
	<tr><td>Source Name</td>  <td> <input type='text' name='source_name' value=''>  </td></tr>
	<tr><td>Nick Name</td>    <td> <input type='text' name='source_nickname' value=''> </td></tr>
	<tr><td>Source Desc</td>     <td>  <textarea rows="5" cols="40" name='source_desc'></textarea>    </td></tr>
	<tr><td align='right' colspan='2'>
	    <input type='hidden' name='form_target' value='findingsource'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Create' src='images/button_create.png'>
	</td></tr>
	</form>

</table>