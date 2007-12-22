<table>

	<form name='poamevidence_update' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='ev_id' value='{$ev_id}'>
	<tr><td>Poam ID</td>    <td> {html_options options=$poam_list selected=$poam_id name='poam_id'}   </td></tr>
	<tr><td>Submission</td>    <td> <input type='text' name='ev_submission' value='{$ev_submission}'>   </td></tr>
	<tr><td>Submission By</td><td> {html_options options=$user_list selected=$ev_submitted_by name='ev_submitted_by'}     </td></tr>
	<tr><td>Date Submitted</td>     <td> {html_select_date prefix="ev_date_submitted_" time=$ev_date_submitted start_year="-5" end_year="+1"}   </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>SSO Evaluation</td>         <td> {html_options values=$evaluation_list output=$evaluation_list selected=$ev_sso_evaluation name='ev_sso_evaluation'}</td></tr>
	<tr><td>Date SSO Evaluation</td>  <td> {html_select_date prefix="ev_date_sso_evaluation_" time=$ev_date_sso_evaluation start_year="-5" end_year="+1"}{html_select_time prefix="ev_date_sso_evaluation_" time=$ev_date_sso_evaluation}  </td></tr>
	<tr><td>FSA Evaluation</td>  <td> {html_options values=$evaluation_list output=$evaluation_list selected=$ev_fsa_evaluation name='ev_fsa_evaluation'}</td></tr>
	<tr><td>Date FSA Evaluation</td>       <td> {html_select_date prefix="ev_date_fsa_evaluation_" time=$ev_date_fsa_evaluation start_year="-5" end_year="+1"}{html_select_time prefix="ev_date_fsa_evaluation_" time=$ev_date_fsa_evaluation}  </td></tr>
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='poamevidence'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Update' src='images/button_update.png'>
		</td>
	</tr>
	</form>

</table>