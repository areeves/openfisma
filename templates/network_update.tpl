<table>

	<form name='network_update' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='network_id' value='{$network_id}'>
	<tr><td>Name</td>    <td> <input type='text' name='network_name' value='{$network_name}'>  </td></tr>
	<tr><td>Nick Name</td><td> <input type='text' name='network_nickname' value='{$network_nickname}'>   </td></tr>
	<tr><td>Desc</td>     <td> <textarea name='network_desc' rows="5" cols="40">{$network_desc}</textarea>  </td></tr>
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='network'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Update' src='images/button_update.png'>
		</td>
	</tr>
	</form>

</table>