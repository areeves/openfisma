{****************************************************************************}
{* Open the list's outer table row.                                         *}
{****************************************************************************}

<tr>
<td>

{****************************************************************************}
{* Open the list's outer table row. Also open up the sort form if it is in  *}
{* standalone mode (no filter or pager to open the form.                    *}
{****************************************************************************}

{if $sort_standalone == '1'}
<form action='{$this_page}' method='POST'>
{/if}

<table border='0' cellpadding='2' cellspacing='0'>

	<tr><td colspan='9'><hr></td></tr>

	<tr>

        <input type='hidden' name='form_target' value='user'>
		<input type='hidden' name='sort_params' value='{$sort_params}'>

		<th align='left'>UserNameFirst
			<input type='image' src='images/button_up.png'   name='sort_params' value='user_name_first ASC'>
			<input type='image' src='images/button_down.png' name='sort_params' value='user_name_first DESC'>
		</th>

		<th align='left'>UserNameLast
			<input type='image' src='images/button_up.png'   name='sort_params' value='user_name_last ASC'>
			<input type='image' src='images/button_down.png' name='sort_params' value='user_name_last DESC'>
		</th>

		<th align='left'>UserName
			<input type='image' src='images/button_up.png'   name='sort_params' value='user_name ASC'>
			<input type='image' src='images/button_down.png' name='sort_params' value='user_name DESC'>
		</th>

		<th colspan='3'>Action</th>

	</tr>

    {****************************************************************************}
    {* Close the form used by the filter, pager and column sort.                *}
    {****************************************************************************}

	</form>

	<tr><td colspan='9'><hr></td></tr>

	{* loop through the user list and populate with values *}
	{section name=user loop=$user_list}

	<tr bgcolor='{cycle values="#EEEEEE,#CCCCCC"}' >


		<td> {$user_list[user].user_name_first} </td>
		<td> {$user_list[user].user_name_last} </td>
		<td> {$user_list[user].user_name} </td>

		<form name='system_view' method='post' action='{$this_page}'>
		<td>
            <input type='hidden' name='form_target' value='user'>
			<input type='hidden' name='referrer'    value='{$this_page}'>
			<input type='hidden' name='user_id'   value='{$user_list[user].user_id}'>
			<input type='image'  name='form_action' value='V' src='images/button_view.png'>
		</td>
		</form>

		<form name='system_update' method='post' action='{$this_page}'>
		<td>
            <input type='hidden' name='form_target' value='user'>
			<input type='hidden' name='referrer'    value='{$this_page}'>
			<input type='hidden' name='user_id'   value='{$user_list[user].user_id}'>
			<input type='image'  name='form_action' value='U' src='images/button_update.png'>
		</td>
		</form>

		<form name='system_delete' method='post' action='{$this_page}'>
		<td>
            <input type='hidden' name='form_target' value='user'>
			<input type='hidden' name='referrer'    value='{$this_page}'>
			<input type='hidden' name='user_id'   value='{$user_list[user].user_id}'>
			<input type='image'  name='form_action' value='D' src='images/button_delete.png'>
		</td>
		</form>
		
	</tr>
	
	{/section}

	<tr><td colspan='9'><hr></td></tr>
	
	<tr>

		<form name='system_create' method='post' action='{$this_page}'>
		<td colspan='9'>
            <input type='hidden' name='form_target' value='user'>
			<input type='hidden' name='referrer'    value='{$this_page}'>
			<input type='image'  name='form_action' value='Create' src='images/button_create.png'>
		</td>
		</form>
	
	</tr>

</table>


{****************************************************************************}
{* Close the list's outer table row and close the table.                    *}
{****************************************************************************}

</td>
</tr>
</table>