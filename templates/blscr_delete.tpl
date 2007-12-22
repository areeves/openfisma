<table>

	<form name='blscr_delete' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='blscr_number' value='{$blscr_number}'>

	<tr><td>Class</td>    <td> {$blscr_class}      </td></tr>
	<tr><td>Subclass</td><td> {$blscr_subclass}     </td></tr>
	<tr><td>Family</td>     <td> {$blscr_family}       </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Control</td>         <td> {$blscr_control}           </td></tr>
	<tr><td>Guidance</td>  <td> {$blscr_guidance}    </td></tr>
	<tr><td>Low</td>  <td> {$blscr_low}    </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Moderate</td>     <td> {$blscr_moderate}            </td></tr>
	<tr><td>High</td>  <td> {$blscr_high}    </td></tr>
	<tr><td>Enhancements</td>    <td> {$blscr_enhancements} </td></tr>
	<tr><td>Supplement</td>       <td> {$blscr_supplement}       </td></tr>
	
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='blscr'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Delete' src='images/button_delete.png'>
		</td>
	</tr>
	</form>

</table>