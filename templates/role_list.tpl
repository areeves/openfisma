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

        <input type='hidden' name='form_target' value='role'>
		<input type='hidden' name='sort_params' value='{$sort_params}'>

		<th align='left'>RoleName
			<input type='image' src='images/button_up.png'   name='sort_params' value='role_name ASC'>
			<input type='image' src='images/button_down.png' name='sort_params' value='role_name DESC'>
		</th>

		<th align='left'>RoleNickname
			<input type='image' src='images/button_up.png'   name='sort_params' value='role_nickname ASC'>
			<input type='image' src='images/button_down.png' name='sort_params' value='role_nickname DESC'>
		</th>

		<th align='left'>RoleDesc
			<input type='image' src='images/button_up.png'   name='sort_params' value='role_desc ASC'>
			<input type='image' src='images/button_down.png' name='sort_params' value='role_desc DESC'>
		</th>

		<th colspan='3'>Action</th>

	</tr>

    {****************************************************************************}
    {* Close the form used by the filter, pager and column sort.                *}
    {****************************************************************************}

	</form>

	<tr><td colspan='9'><hr></td></tr>

	{* loop through the role list and populate with values *}
	{section name=role loop=$role_list}

	<tr bgcolor='{cycle values="#EEEEEE,#CCCCCC"}' >


		<td> {$role_list[role].role_name} </td>
		<td> {$role_list[role].role_nickname} </td>
		<td> {$role_list[role].role_desc} </td>

		<form name='system_view' method='post' action='{$this_page}'>
		<td>
            <input type='hidden' name='form_target' value='role'>
			<input type='hidden' name='referrer'    value='{$this_page}'>
			<input type='hidden' name='role_id'   value='{$role_list[role].role_id}'>
			<input type='image'  name='form_action' value='V' src='images/button_view.png'>
		</td>
		</form>

		<form name='system_update' method='post' action='{$this_page}'>
		<td>
            <input type='hidden' name='form_target' value='role'>
			<input type='hidden' name='referrer'    value='{$this_page}'>
			<input type='hidden' name='role_id'   value='{$role_list[role].role_id}'>
			<input type='image'  name='form_action' value='U' src='images/button_update.png'>
		</td>
		</form>

		<form name='system_delete' method='post' action='{$this_page}'>
		<td>
            <input type='hidden' name='form_target' value='role'>
			<input type='hidden' name='referrer'    value='{$this_page}'>
			<input type='hidden' name='role_id'   value='{$role_list[role].role_id}'>
			<input type='image'  name='form_action' value='D' src='images/button_delete.png'>
		</td>
		</form>
		
	</tr>
	
	{/section}

	<tr><td colspan='9'><hr></td></tr>
	
	<tr>

		<form name='system_create' method='post' action='{$this_page}'>
		<td colspan='9'>
            <input type='hidden' name='form_target' value='role'>
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