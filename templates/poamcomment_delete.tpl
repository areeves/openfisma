<table>

	<form name='poamcomment_delete' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='comment_id' value='{$comment_id}'>
	<tr><td>Poam ID</td>    <td> {$poam_id}      </td></tr>
	<tr><td>User ID</td><td> {$user_id}     </td></tr>
	<tr><td>Comment Parent</td>     <td> {$comment_parent}       </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Comment Date</td>         <td> {$comment_date}           </td></tr>
	<tr><td>Comment Topic</td>  <td> {$comment_topic}    </td></tr>
	<tr><td>Comment Body</td>  <td> {$comment_body}    </td></tr>
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='poamcomment'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Delete' src='images/button_delete.png'>
		</td>
	</tr>
	</form>

</table>