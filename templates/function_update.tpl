<table>

	<form name='function_update' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='function_id' value='{$function_id}'>

	<tr><td>Name</td>    <td> <input type='text' name='function_name' value='{$function_name}'>      </td></tr>
	<tr><td>Screen</td>     <td> <input type='text' name='function_screen' value='{$function_screen}'>            </td></tr>
	<tr><td>Action</td>  <td> <input type='text' name='function_action' value='{$function_action}'>    </td></tr>
	<tr><td>Desc</td>    <td> <textarea name='function_desc' rows="5" cols="40">{$function_desc}</textarea> </td></tr>
	<tr><td>Open</td>       <td> <input type='text' name='function_open' value='{$function_open}'>       </td></tr>
		
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='function'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Update' src='images/button_update.png'>
		</td>
	</tr>
	</form>

</table>