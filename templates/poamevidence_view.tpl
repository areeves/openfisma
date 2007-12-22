<table>

	<tr><td>Poam ID</td>    <td> {$poam_id}      </td></tr>
	<tr><td>Submission</td>    <td> {$ev_submission}      </td></tr>
	<tr><td>Submission By</td><td> {$ev_submitted_by}     </td></tr>
	<tr><td>Date Submitted</td>     <td> {$ev_date_submitted}       </td></tr>
	
	<tr><td colspan='4'><br></td></tr>
	
	<tr>
		<td> SSO Evaluation</td>
		<td> {$ev_sso_evaluation} </td>
		<td> Date: </td> 
		<td> {$ev_date_sso_evaluation}</td>

	</tr>

	<tr>
		<td> FSA Evaluation</td>      
		<td> {$ev_fsa_evaluation} </td>
		<td> Date: </td>
		<td> {$ev_date_fsa_evaluation} </td>

	</tr>

	<tr>

		<td> IV&V Evaluation</td>
		<td> {$ev_ivv_evaluation} </td>
		<td> Date: </td> 
		<td> {$ev_date_ivv_evaluation} </td>

	</tr>
	
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
			<form name='poamevidence_list' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='poamevidence'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='ev_id'     value='{$ev_id}'>
				<input type='image'  name='form_action' value='Cancel' src='images/button_cancel.png'>
			</form>
		
		</td>
		{/if}

		{if $show_update == '1'}
		<td>
			<form name='poamevidence_update' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='poamevidence'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='ev_id'     value='{$ev_id}'>
				<input type='image'  name='form_action' value='Update' src='images/button_update.png'>
			</form>
			
		</td>
		{/if}
	
		{if $show_update == '1'}
		<td>
			<form name='poamevidence_delete' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='poamevidence'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='ev_id'     value='{$ev_id}'>
				<input type='image'  name='form_action' value='Delete' src='images/button_delete.png'>
			</form>
		
		</td>
		{/if}
		
	</tr>
	
</table>
{/if}