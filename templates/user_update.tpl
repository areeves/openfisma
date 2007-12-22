<table>

	<form name='user_update' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='user_id' value='{$user_id}'>
	<tr><td>First Name</td>    <td> <input type='text'     name='user_name_first'   value='{$user_name_first}'>  </td></tr>
	<tr><td>Middle Initial</td><td> <input type='text'     name='user_name_middle'  value='{$user_name_middle}'> </td></tr>
	<tr><td>Last Name</td>     <td> <input type='text'     name='user_name_last'    value='{$user_name_last}'>   </td></tr>
	<tr><td colspan='2'><br><tr>
	<tr><td>Title</td>         <td> <input type='text'     name='user_title'        value='{$user_title}'>       </td></tr>
	<tr><td>Office Phone</td>  <td> <input type='text'     name='user_phone_office' value='{$user_phone_office}'></td></tr>
	<tr><td>Mobile Phone</td>  <td> <input type='text'     name='user_phone_mobile' value='{$user_phone_mobile}'></td></tr>
	<tr><td colspan='2'><br><tr>
	<tr><td>Username</td>      <td> <input type='text'     name='user_name'         value='{$user_name}'></td></tr>
		<tr><td>Role ID</td>      <td> {html_options options=$role_list selected=$role_id name='role_id'}
 </td></tr>

	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='user'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Update' src='images/button_update.png'>
		</td>
	</tr>
	</form>

</table>