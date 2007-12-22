<table>

	<form name='network_delete' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='network_id' value='{$network_id}'>
		
	<tr><td>Name</td>    <td> {$network_name}      </td></tr>
	<tr><td>Nick Name</td><td> {$network_nickname}     </td></tr>
	<tr><td>Desc</td>     <td> {$network_desc}       </td></tr>
	
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='network'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Delete' src='images/button_delete.png'>
		</td>
	</tr>
	</form>

</table>