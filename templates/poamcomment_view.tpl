<table>

	<tr><td>Poam ID</td>    <td> {$poam_id}      </td></tr>
	<tr><td>User ID</td><td> {$user_id}     </td></tr>
	<tr><td>Comment Parent</td>     <td> {$comment_parent}       </td></tr>
	
	<tr><td colspan='2'><br></td></tr>
	
	<tr><td>Comment Date</td>         <td> {$comment_date}           </td></tr>
	<tr><td>Comment Topic</td>  <td> {$comment_topic}    </td></tr>
	<tr><td>Comment Body</td>  <td> {$comment_body}    </td></tr>

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
			<form name='poamcomment_list' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='poamcomment'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='comment_id'     value='{$comment_id}'>
				<input type='image'  name='form_action' value='Cancel' src='images/button_cancel.png'>
			</form>
		
		</td>
		{/if}

		{if $show_update == '1'}		
		<td>
			
			<form name='poamcomment_update' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='poamcomment'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='comment_id'     value='{$comment_id}'>
				<input type='image'  name='form_action' value='Update' src='images/button_update.png'>
			</form>
			
		</td>
		{/if}
	
		{if $show_delete == '1'}
		<td>
			<form name='poamcomment_delete' method='post' action='{$this_page}'>
        	    <input type='hidden' name='form_target' value='poamcomment'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='hidden' name='comment_id'     value='{$comment_id}'>
				<input type='image'  name='form_action' value='Delete' src='images/button_delete.png'>
			</form>
		
		</td>
		{/if}
		
	</tr>
	
</table>
{/if}