<table>

	<form name='poamcomment_create' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
	<tr><td>Poam ID</td>    <td>  {html_options options=$poam_list selected=$poam_id name='poam_id'}   </td></tr>
	<tr><td>User ID</td><td> {html_options options=$user_list selected=$user_id name='user_id'}  </td></tr>
	<tr><td>Comment Parent</td>     <td> <input type='text'     name='comment_parent'   value='0'>       </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Comment Date</td> <td> 
{html_select_date prefix="comment_date_" time=$comment_date start_year="-5" end_year="+1"}{html_select_time prefix="comment_date_" time=$comment_date}
	</td></tr>
	<tr><td>Comment Topic</td>  <td> <input type='text'     name='comment_topic'   value=''>    </td></tr>
	<tr><td>Comment Body</td>  <td>  <textarea name='comment_body' rows="5" cols="40">{$comment_body}</textarea>  </td></tr>
	<tr><td align='right' colspan='2'>
	    <input type='hidden' name='form_target' value='poamcomment'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Create' src='images/button_create.png'>
	</td></tr>
	</form>

</table>