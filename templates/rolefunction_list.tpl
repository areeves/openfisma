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

        <input type='hidden' name='form_target' value='rolefunction'>
		<input type='hidden' name='sort_params' value='{$sort_params}'>

		<th align='left'>RoleId
			<input type='image' src='images/button_up.png'   name='sort_params' value='role_id ASC'>
			<input type='image' src='images/button_down.png' name='sort_params' value='role_id DESC'>
		</th>

		<th align='left'>FunctionId
			<input type='image' src='images/button_up.png'   name='sort_params' value='function_id ASC'>
			<input type='image' src='images/button_down.png' name='sort_params' value='function_id DESC'>
		</th>

		<th colspan='3'>Action</th>

	</tr>

    {****************************************************************************}
    {* Close the form used by the filter, pager and column sort.                *}
    {****************************************************************************}

	</form>

	<tr><td colspan='9'><hr></td></tr>

	{* loop through the rolefunction list and populate with values *}
	{section name=rolefunction loop=$rolefunction_list}

	<tr bgcolor='{cycle values="#EEEEEE,#CCCCCC"}' >


		<td> {$rolefunction_list[rolefunction].role_id} </td>
		<td> {$rolefunction_list[rolefunction].function_id} </td>

		<form name='system_view' method='post' action='{$this_page}'>
		<td>
            <input type='hidden' name='form_target' value='rolefunction'>
			<input type='hidden' name='referrer'    value='{$this_page}'>
			<input type='hidden' name='role_func_id'   value='{$rolefunction_list[rolefunction].role_func_id}'>
			<input type='image'  name='form_action' value='V' src='images/button_view.png'>
		</td>
		</form>

		<form name='system_update' method='post' action='{$this_page}'>
		<td>
            <input type='hidden' name='form_target' value='rolefunction'>
			<input type='hidden' name='referrer'    value='{$this_page}'>
			<input type='hidden' name='role_func_id'   value='{$rolefunction_list[rolefunction].role_func_id}'>
			<input type='image'  name='form_action' value='U' src='images/button_update.png'>
		</td>
		</form>

		<form name='system_delete' method='post' action='{$this_page}'>
		<td>
            <input type='hidden' name='form_target' value='rolefunction'>
			<input type='hidden' name='referrer'    value='{$this_page}'>
			<input type='hidden' name='role_func_id'   value='{$rolefunction_list[rolefunction].role_func_id}'>
			<input type='image'  name='form_action' value='D' src='images/button_delete.png'>
		</td>
		</form>
		
	</tr>
	
	{/section}

	<tr><td colspan='9'><hr></td></tr>
	
	<tr>

		<form name='system_create' method='post' action='{$this_page}'>
		<td colspan='9'>
            <input type='hidden' name='form_target' value='rolefunction'>
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