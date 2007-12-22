<table>

    <tr><td>Finding ID</td> <td> {$id} </td></tr>
	<tr><td>Source ID</td>  <td> {$source_id} </td></tr>
	<tr><td>Asset ID</td>   <td> {$asset_id} </td></tr>
	<tr><td>Status</td>     <td> {$finding_status} </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Date Created</td>    <td> {$finding_date_created} </td></tr>
	<tr><td>Date Discovered</td> <td> {$finding_date_discovered} </td></tr>
	<tr><td>Date Closed</td>     <td> {$finding_date_closed} </td></tr>
	<tr><td>Data</td>            <td> {$finding_data} </td></tr>
	
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
			<form name='finding_list' method='post' action='finding_list.php'>
        	    <input type='hidden' name='form_target' value='finding'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='id'          value='{$id}'>
				<input type='image'  name='form_action' value='Cancel' src='images/button_cancel.png'>
			</form>
		
		</td>
		{/if}
		
		{if $show_update == '1'}
		<td>
			
			<form name='finding_update' method='post' action='finding_update.php'>
        	    <input type='hidden' name='form_target' value='finding'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='id'          value='{$id}'>
				<input type='image'  name='form_action' value='Update' src='images/button_update.png'>
			</form>
			
		</td>
		{/if}
	
		{if $show_delete == '1'}
		<td>
			<form name='finding_delete' method='post' action='finding_delete.php'>
			    <input type='hidden' name='form_target' value='finding'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='id'          value='{$id}'>
				<input type='image'  name='form_action' value='Delete' src='images/button_delete.png'>
			</form>
		
		</td>
		{/if}
		
	</tr>
	
</table>
{/if}