<table>

	<form name='function_create' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>

	<tr><td>Name</td>    <td> <input type='text' name='function_name' value=''>      </td></tr>
	<tr><td>Screen</td>     <td> <input type='text' name='function_screen' value=''>            </td></tr>
	<tr><td>Action</td>  <td> <input type='text' name='function_action' value=''>    </td></tr>
	<tr><td>Desc</td>    <td> <textarea name='function_desc' rows="5" cols="40"></textarea> </td></tr>
	<tr><td>Open</td>       <td> <input type='text' name='function_open' value='0'>       </td></tr>
		
	<tr><td align='right' colspan='2'>
	    <input type='hidden' name='form_target' value='function'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Create' src='images/button_create.png'>
	</td></tr>
	</form>

</table>