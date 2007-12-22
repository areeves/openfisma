<table>

	<form name='asset_delete' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='asset_id' value='{$asset_id}'>
	<tr><td>Asset Name</td>  <td> {$asset_name}         </td></tr>
	<tr><td>Prod ID</td>        <td> {$prod_id}         </td></tr>
	<tr><td>Date Created</td><td> {$asset_date_created} </td></tr>
	<tr><td>Source</td>      <td> {$asset_source}       </td></tr>
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='asset'>
		<input type='image' name='form_action' value='cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='delete' src='images/button_delete.png'>
		</td>
	</tr>
	</form>

</table>