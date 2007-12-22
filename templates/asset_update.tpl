<table>

	<form name='asset_update' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='asset_id' value='{$asset_id}'>
	<tr><td>Prod ID</td> <td> {html_options options=$prod_list name='prod_id' selected=$prod_id} </td></tr>
	<tr><td>Asset Name</td> <td> <input type='text' name='asset_name'   value='{$asset_name}'>  </td></tr>
	<tr><td>Source</td>     <td>{html_options values=$asset_source_list output=$asset_source_list name='asset_source' selected=$asset_source}</td></tr>
	
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='asset'>
		<input type='image' name='form_action' value='cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='update' src='images/button_update.png'>
		</td>
	</tr>
	</form>

</table>