<table>

	<form name='asset_create' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
	<tr><td>Prod ID</td> <td>  {html_options options=$prod_list name='prod_id'}   </td></tr>
	<tr><td>Asset Name</td> <td> <input type='text' name='asset_name'>   </td></tr>
	<tr><td>Source</td>     <td>{html_options values=$asset_source_list output=$asset_source_list name='asset_source' selected=$asset_source}</td></tr>
	<tr><td align='right' colspan='2'>
	    <input type='hidden' name='form_target' value='asset'>
		<input type='image' name='form_action' value='cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='create' src='images/button_create.png'>
	</td></tr>
	</form>

</table>