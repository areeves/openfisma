<table>

	<form name='plugin_update' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='plugin_id' value='{$plugin_id}'>

	<tr><td>Name</td>    <td> <input type='text' name='plugin_name' value='{$plugin_name}'>      </td></tr>
	<tr><td>Nick Name</td><td> <input type='text' name='plugin_nickname' value='{$plugin_nickname}'>     </td></tr>
	<tr><td>Abbreviation</td>     <td> <input type='text' name='plugin_abbreviation' value='{$plugin_abbreviation}'>       </td></tr>
	<tr><td>Desc</td>       <td> <textarea name='plugin_desc' rows="5" cols="40">{$plugin_desc}</textarea>       </td></tr>
		
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='plugin'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Update' src='images/button_update.png'>
		</td>
	</tr>
	</form>

</table>