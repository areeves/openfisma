<table>

	<tr><td>Name</td>    <td> {$system_name}      </td></tr>
	<tr><td>Nick Name</td><td> {$system_nickname}     </td></tr>
	<tr><td>Desc</td>     <td> {$system_desc}       </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Type</td>         <td> {$system_type}           </td></tr>
	<tr><td>Primary Office</td>  <td> {$system_primary_office}    </td></tr>
	<tr><td>Tier</td>    <td> {$system_tier} </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Confidentiality</td>  <td> {$system_confidentiality}    </td></tr>
	<tr><td>Integrity</td>     <td> {$system_integrity}            </td></tr>
	<tr><td>Availability</td>  <td> {$system_availability}    </td></tr>

	<tr><td colspan='2'><br></td></tr>

	<tr><td>Criticality Justification</td>       <td> {$system_criticality_justification}       </td></tr>
	<tr><td>Sensitivity Justification</td>       <td> {$system_sensitivity_justification}       </td></tr>
	
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
			<form name='system_list' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='system'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='system_id'   value='{$system_id}'>
				<input type='image'  name='form_action' value='Cancel' src='images/button_cancel.png'>
			</form>
		
		</td>
		{/if}

		{if $show_update == '1'}		
		<td>
			
			<form name='system_update' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='system'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='system_id'   value='{$system_id}'>
				<input type='image'  name='form_action' value='Update' src='images/button_update.png'>
			</form>
			
		</td>
		{/if}

		{if $show_delete == '1'}	
		<td>
			<form name='system_delete' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='system'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='system_id'   value='{$system_id}'>
				<input type='image'  name='form_action' value='Delete' src='images/button_delete.png'>
			</form>
		
		</td>
		{/if}
		
	</tr>
	
</table>
{/if}