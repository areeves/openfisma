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

	<tr><td colspan='10'><hr></td></tr>

	{************************************************************************}
	{* COLUMN HEADERS                                                       *}
	{************************************************************************}

	<tr>

		<input type='hidden' name='sort_params' value='{$sort_params}'>

		<th>ID
			<input type='image' src='images/button_up.png'   name='sort_params' value='poam_id ASC'>
			<input type='image' src='images/button_down.png' name='sort_params' value='poam_id DESC'>
		</th>

		<th>Audit
			<input type='image' src='images/button_up.png'   name='sort_params' value='finding_source ASC'>
			<input type='image' src='images/button_down.png' name='sort_params' value='finding_source DESC'>
		</th>

		<th>Finding ID
			<input type='image' src='images/button_up.png'   name='sort_params' value='finding_id ASC'>
			<input type='image' src='images/button_down.png' name='sort_params' value='finding_id DESC'>
		</th>

		<th>Type
			<input type='image' src='images/button_up.png'   name='sort_params' value='poam_type ASC'>
			<input type='image' src='images/button_down.png' name='sort_params' value='poam_type DESC'>
		</th>

		<th>Status
			<input type='image' src='images/button_up.png'   name='sort_params' value='poam_status ASC'>
			<input type='image' src='images/button_down.png' name='sort_params' value='poam_status DESC'>
		</th>

		<th>System
			<input type='image' src='images/button_up.png'   name='sort_params' value='poam_action_owner ASC'>
			<input type='image' src='images/button_down.png' name='sort_params' value='poam_action_owner DESC'>
		</th>

		<th>Due Date
			<input type='image' src='images/button_up.png'   name='sort_params' value='poam_action_date_est ASC'>
			<input type='image' src='images/button_down.png' name='sort_params' value='poam_action_date_est DESC'>
		</th>

		<th colspan='3'>Action</th>

	</tr>

	<tr><td colspan='10'><hr></td></tr>

	{************************************************************************}
	{* LIST ROWS                                                            *}
	{************************************************************************}

	{section name=remediation loop=$remediation_list}

	<tr bgcolor='{cycle values="#EEEEEE,#CCCCCC"}'>

		<td> {$remediation_list[remediation].poam_id } </td>
		<td> {$remediation_list[remediation].finding_source } </td>
		<td> {$remediation_list[remediation].finding_id} </td>
		<td> {$remediation_list[remediation].poam_type} </td>
		<td> {$remediation_list[remediation].poam_status} </td>
		<td> {$remediation_list[remediation].poam_action_owner} </td>
		<td> {$remediation_list[remediation].poam_action_date_est} </td>


		<form name='remediation_view' method='post' action='remediation_view.php'>
		<td>
			<input type='hidden' name='referrer'    value='{$this_page}'>
			<input type='hidden' name='poam_id'     value='{$remediation_list[remediation].poam_id}'>
			<input type='hidden' name='form_target' value='remediation'>
			<input type='image'  name='form_action' value='V' src='images/button_view.png'>
		</td>
		</form>
		
	</tr>
	
	{/section}

	<tr><td colspan='10'><hr></td></tr>	

</table>

<table>

	<tr>
	
		<td>
		
			<form name='poam_create' method='post' action='poam_create.php'>
				<input type='hidden' name='referrer'    value='{$this_page}'>
				<input type='image'  name='form_action' value='Create' src='images/button_create.png'>
			</form>
		
		</td>	
	
	</tr>

</table>


{****************************************************************************}
{* Close the list's outer table row and close the table.                    *}
{****************************************************************************}

</td>
</tr>
</table>