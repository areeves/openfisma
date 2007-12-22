<table>

	<form name='systemasset_update' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='systemasset_id' value='{$systemasset_id}'>
	<tr><td>System ID</td>    <td> <input type="text" name="system_id" value="{$system_id}">      </td></tr>
	<tr><td>Asset ID</td><td> <input type="text" name="asset_id" value="{$asset_id}">     </td></tr>
	<tr><td>System Is Owner</td>     <td> <input type="text" name="system_is_owner" value="{$system_is_owner}">       </td></tr>
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='systemasset'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Update' src='images/button_update.png'>
		</td>
	</tr>
	</form>

</table>