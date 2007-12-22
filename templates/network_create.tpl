<table>

	<form name='network_create' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
	<tr><td>Name</td>    <td> <input type='text' name='network_name' value=''>  </td></tr>
	<tr><td>Nick Name</td><td> <input type='text' name='network_nickname' value=''>   </td></tr>
	<tr><td>Desc</td>     <td> <textarea name='network_desc' rows="5" cols="40"></textarea>  </td></tr>
	<tr><td align='right' colspan='2'>
	    <input type='hidden' name='form_target' value='network'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Create' src='images/button_create.png'>
	</td></tr>
	</form>

</table>