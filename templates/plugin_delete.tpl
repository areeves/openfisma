<table>

	<form name='plugin_delete' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='plugin_id' value='{$plugin_id}'>
	<tr><td>Name</td>    <td> {$plugin_name}      </td></tr>
	<tr><td>Nick Name</td><td> {$plugin_nickname}     </td></tr>
	<tr><td>Abbreviation</td>     <td> {$plugin_abbreviation}       </td></tr>
	<tr><td>Desc</td>       <td> {$plugin_desc}       </td></tr>
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='plugin'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Delete' src='images/button_delete.png'>
		</td>
	</tr>
	</form>

</table>