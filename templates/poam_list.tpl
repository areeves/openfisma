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

        <input type='hidden' name='form_target' value='poam'>
		<input type='hidden' name='sort_params' value='{$sort_params}'>

		<th align='left'>FindingId
			<input type='image' src='images/button_up.png'   name='sort_params' value='finding_id ASC'>
			<input type='image' src='images/button_down.png' name='sort_params' value='finding_id DESC'>
		</th>

		<th align='left'>PoamType
			<input type='image' src='images/button_up.png'   name='sort_params' value='poam_type ASC'>
			<input type='image' src='images/button_down.png' name='sort_params' value='poam_type DESC'>
		</th>

		<th align='left'>PoamStatus
			<input type='image' src='images/button_up.png'   name='sort_params' value='poam_status ASC'>
			<input type='image' src='images/button_down.png' name='sort_params' value='poam_status DESC'>
		</th>

		<th align='left'>PoamActionOwner
			<input type='image' src='images/button_up.png'   name='sort_params' value='poam_action_owner ASC'>
			<input type='image' src='images/button_down.png' name='sort_params' value='poam_action_owner DESC'>
		</th>

		<th align='left'>PoamActionDateEst
			<input type='image' src='images/button_up.png'   name='sort_params' value='poam_action_date_est ASC'>
			<input type='image' src='images/button_down.png' name='sort_params' value='poam_action_date_est DESC'>
		</th>

		<th colspan='3'>Action</th>

	</tr>

    {****************************************************************************}
    {* Close the form used by the filter, pager and column sort.                *}
    {****************************************************************************}

	</form>

	<tr><td colspan='9'><hr></td></tr>

	{* loop through the poam list and populate with values *}
	{section name=poam loop=$poam_list}

	<tr bgcolor='{cycle values="#EEEEEE,#CCCCCC"}' >


		<td> {$poam_list[poam].finding_id} </td>
		<td> {$poam_list[poam].poam_type} </td>
		<td> {$poam_list[poam].poam_status} </td>
		<td> {$poam_list[poam].poam_action_owner} </td>
		<td> {$poam_list[poam].poam_action_date_est} </td>

		<form name='system_view' method='post' action='poam_view.php'>
		<td>
            <input type='hidden' name='form_target' value='poam'>
			<input type='hidden' name='referrer'    value='{$this_page}'>
			<input type='hidden' name='poam_id'   value='{$poam_list[poam].poam_id}'>
			<input type='image'  name='form_action' value='V' src='images/button_view.png'>
		</td>
		</form>

		<form name='system_update' method='post' action='poam_update.php'>
		<td>
            <input type='hidden' name='form_target' value='poam'>
			<input type='hidden' name='referrer'    value='{$this_page}'>
			<input type='hidden' name='poam_id'   value='{$poam_list[poam].poam_id}'>
			<input type='image'  name='form_action' value='U' src='images/button_update.png'>
		</td>
		</form>

		<form name='system_delete' method='post' action='poam_delete.php'>
		<td>
            <input type='hidden' name='form_target' value='poam'>
			<input type='hidden' name='referrer'    value='{$this_page}'>
			<input type='hidden' name='poam_id'   value='{$poam_list[poam].poam_id}'>
			<input type='image'  name='form_action' value='D' src='images/button_delete.png'>
		</td>
		</form>
		
	</tr>
	
	{/section}

	<tr><td colspan='9'><hr></td></tr>
	
	<tr>

		<form name='system_create' method='post' action='poam_create.php'>
		<td colspan='9'>
            <input type='hidden' name='form_target' value='poam'>
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