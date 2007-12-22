<table>

	<form name='poamevidence_delete' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='ev_id' value='{$ev_id}'>
	<tr><td>Poam ID</td>    <td> {$poam_id}      </td></tr>
	<tr><td>Submission</td>    <td> {$ev_submission}      </td></tr>
	<tr><td>Submission By</td><td> {$ev_submitted_by}     </td></tr>
	<tr><td>Date Submitted</td>     <td> {$ev_date_submitted}       </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>SSO Evaluation</td>         <td> {$ev_sso_evaluation}           </td></tr>
	<tr><td>Date SSO Evaluation</td>  <td> {$ev_date_sso_evaluation}    </td></tr>
	<tr><td>FSA Evaluation</td>  <td> {$ev_fsa_evaluation}    </td></tr>
	<tr><td>Date FSA Evaluation</td>       <td> {$ev_date_fsa_evaluation}       </td></tr>
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='poamevidence'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Delete' src='images/button_delete.png'>
		</td>
	</tr>
	</form>

</table>