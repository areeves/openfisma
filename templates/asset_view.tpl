<table>

	<tr><td>Name</td>        <td> {$asset_name}         </td></tr>
	<tr><td>Prod ID</td>        <td> {$prod_id}         </td></tr>
	<tr><td>Date Created</td><td> {$asset_date_created} </td></tr>
	<tr><td>Source</td>      <td> {$asset_source}       </td></tr>
	
</table>


{******************************************************************************}
{* The options listed here are conditional so that they may be utilized in    *}
{* other portions of the application without allowing administrative style    *}
{* updates, deletes or cancellations (referrer handling, return to caller).   *}
{******************************************************************************}

{if $show_cancel == '1' || $show_update == '1' || $show_delete == '1'}
<table>

	<tr>

		{if $show_cancel == '1'}
		<td>
			<form name='asset_list' method='post' action='{$this_page}'>
	            <input type='hidden' name='form_target' value='asset'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='asset_id'    value='{$asset_id}'>
				<input type='image'  name='form_action' value='cancel' src='images/button_cancel.png'>
			</form>
		
		</td>
		{/if}
		
		{if $show_update == '1'}
		<td>
			
			<form name='asset_update' method='post' action='{$this_page}'>
	            <input type='hidden' name='form_target' value='asset'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='asset_id'    value='{$asset_id}'>
				<input type='image'  name='form_action' value='update' src='images/button_update.png'>
			</form>
			
		</td>
		{/if}
	
		{if $show_delete == '1'}
		<td>
			<form name='asset_delete' method='post' action='{$this_page}'>
	            <input type='hidden' name='form_target' value='asset'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='asset_id'    value='{$asset_id}'>
				<input type='image'  name='form_action' value='delete' src='images/button_delete.png'>
			</form>
		
		</td>
		{/if}
		
	</tr>
	
</table>
{/if}
