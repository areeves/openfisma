<table>

	<form name='blscr_create' method='post' action='{$this_page}'>
		<input type='hidden' name='referrer' value='{$this_page}'>

	<tr><td><b>Number</b></td>     <td> <input type='text' name='blscr_number' value='{$rand_key}'>           </td></tr>
	<tr><td>Class</td>    <td> {html_options values=$blscr_class_list output=$blscr_class_list selected=$blscr_class name='blscr_class'}</td></tr>
	<tr><td>Subclass</td><td> <textarea name='blscr_subclass' rows="5" cols="40"></textarea>     </td></tr>
	<tr><td>Family</td>     <td> <textarea name='blscr_family' rows="5" cols="40"></textarea>       </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Control</td>         <td> <textarea name='blscr_control' rows="5" cols="40"></textarea>           </td></tr>
	<tr><td>Guidance</td>  <td> <textarea name='blscr_guidance' rows="5" cols="40"></textarea>    </td></tr>
	<tr><td>Low</td>  <td> <input type='text' name='blscr_low' value='0'>    </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Moderate</td>     <td> <input type='text' name='blscr_moderate' value='0'>           </td></tr>
	<tr><td>High</td>  <td> <input type='text' name='blscr_high' value='0'>   </td></tr>
	<tr><td>Enhancements</td>    <td> <textarea name='blscr_enhancements' rows="5" cols="40"></textarea> </td></tr>
	<tr><td>Supplement</td>       <td> <textarea name='blscr_supplement' rows="5" cols="40"></textarea>   
		
	<tr><td align='right' colspan='2'>
	    <input type='hidden' name='form_target' value='blscr'>
		<input type='image' name='form_action' value='Cancel' src='images/button_cancel.png'>
		<input type='image' name='form_action' value='Create' src='images/button_create.png'>
	</td></tr>
	</form>

</table>