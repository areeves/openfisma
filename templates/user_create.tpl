<table>

	<form name='user_create' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
	<tr><td>First Name</td>    <td> <input type='text'     name='user_name_first'> </td></tr>
	<tr><td>Middle Initial</td><td> <input type='text'     name='user_name_middle'></td></tr>
	<tr><td>Last Name</td>     <td> <input type='text'     name='user_name_last'>  </td></tr>
	<tr><td colspan='2'><br><tr>
	<tr><td>Title</td>         <td> <input type='text'     name='user_name'>       </td></tr>
	<tr><td>Office Phone</td>  <td> <input type='text'     name='user_name'>       </td></tr>
	<tr><td>Mobile Phone</td>  <td> <input type='text'     name='user_name'>       </td></tr>
	<tr><td colspan='2'><br><tr>
	<tr><td>Username</td>      <td> <input type='text'     name='user_name'>       </td></tr>
	<tr><td>Password</td>      <td> <input type='password' name='user_password'>   </td></tr>
	<tr><td>Confirmation</td>  <td> <input type='password' name='user_password_confirmation'>   </td></tr>
	<tr><td>Role ID</td>  <td> {html_options options=$role_list name='role_id'}  </td></tr>
	
	<tr><td align='right' colspan='2'>
	    <input type='hidden' name='form_target' value='user'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Create' src='images/button_create.png'>
	</td></tr>
	</form>

</table>