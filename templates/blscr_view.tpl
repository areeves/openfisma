<table>

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
	
</table>


{******************************************************************************}
{* The options listed here are conditional so that they may be utilized in    *}
{* other portions of the application without allowing administrative style    *}
{* updates, deletes or cancellations (referrer handling, return to caller).   *}
{******************************************************************************}

{if $show_cancel == '1' || $show_update == '1' || $show_delete == '1'}
<table>

	<tr>

		{if $show_cancel == '1'}
		<td>
			<form name='blscr_list' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='blscr'>
				<input type='hidden' name='referrer'     value='{$this_page}'>
				<input type='hidden' name='blscr_number' value='{$blscr_number}'>
				<input type='image'  name='form_action'  value='Cancel' src='images/button_cancel.png'>
			</form>
		
		</td>
        {/if}

		{if $show_update == '1'}		
		<td>
			
			<form name='blscr_update' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='blscr'>
				<input type='hidden' name='referrer'     value='{$this_page}'>
				<input type='hidden' name='blscr_number' value='{$blscr_number}'>
				<input type='image'  name='form_action'  value='Update' src='images/button_update.png'>
			</form>
			
		</td>
		{/if}

		{if $show_delete == '1'}	
		<td>
			<form name='blscr_delete' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='blscr'>
				<input type='hidden' name='referrer'     value='{$this_page}'>
				<input type='hidden' name='blscr_number' value='{$blscr_number}'>
				<input type='image'  name='form_action ' value='Delete' src='images/button_delete.png'>
			</form>
		
		</td>
        {/if}
		
	</tr>
	
</table>
{/if}
