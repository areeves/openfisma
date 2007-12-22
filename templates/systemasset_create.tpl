<table>

	<form name='systemasset_create' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
	<tr><td>System ID</td>    <td> <input type="text" name="system_id" value="">      </td></tr>
	<tr><td>Asset ID</td><td> <input type="text" name="asset_id" value="">     </td></tr>
	<tr><td>System Is Owner</td>     <td> <input type="text" name="system_is_owner" value="">       </td></tr>
	<tr><td align='right' colspan='2'>
	    <input type='hidden' name='form_target' value='systemasset'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Create' src='images/button_create.png'>
	</td></tr>
	</form>

</table>