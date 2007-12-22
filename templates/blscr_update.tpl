<table>

	<form name='blscr_update' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>
		<input type='hidden' name='blscr_number' value='{$blscr_number}'>

	<tr><td>Class</td>    <td> {html_options values=$blscr_class_list output=$blscr_class_list selected=$blscr_class name='blscr_class'}</td></tr>
	<tr><td>Subclass</td><td> <textarea name='blscr_subclass' rows="5" cols="40">{$blscr_subclass}</textarea>     </td></tr>
	<tr><td>Family</td>     <td> <textarea name='blscr_family' rows="5" cols="40">{$blscr_family}</textarea>       </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Control</td>         <td> <textarea name='blscr_control' rows="5" cols="40">{$blscr_control}</textarea>           </td></tr>
	<tr><td>Guidance</td>  <td> <textarea name='blscr_guidance' rows="5" cols="40">{$blscr_guidance}</textarea>    </td></tr>
	<tr><td>Low</td>  <td> <input type='text' name='blscr_low' value='{$blscr_low}'>    </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Moderate</td>     <td> <input type='text' name='blscr_moderate' value='{$blscr_moderate}'>           </td></tr>
	<tr><td>High</td>  <td> <input type='text' name='blscr_high' value='{$blscr_high}'>   </td></tr>
	<tr><td>Enhancements</td>    <td> <textarea name='blscr_enhancements' rows="5" cols="40">{$blscr_enhancements}</textarea> </td></tr>
	<tr><td>Supplement</td>       <td> <textarea name='blscr_supplement' rows="5" cols="40">{$blscr_supplement}</textarea>       </td></tr>
	
	<tr><td colspan='2'>
	    <input type='hidden' name='form_target' value='blscr'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Update' src='images/button_update.png'>
		</td>
	</tr>
	</form>

</table>