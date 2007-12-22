{****************************************************************************}
{* Open the list's outer table row.                                         *}
{****************************************************************************}

<tr>
<td>

{****************************************************************************}
{* Open the list's outer table row. Also open up the sort form if it is in  *}
{* standalone mode (no filter or pager to open the form.                    *}
{****************************************************************************}

{if $listing_form_start == '1'}
<form action='{$this_page}' method='POST'>
{/if}

<table border='0' cellpadding='2' cellspacing='2' width='100%'>

	<tr><td colspan='{$column_count}'><hr></td></tr>

	<tr>

        <input type='hidden' name='form_target' value='{$this_page}'>
		<input type='hidden' name='sort_params' value='{$sort_params}'>
		
		{if $show_checkboxes == '1'}<th>&nbsp;</th>{/if}
		
		{foreach from=$columns item=column}

		  {if $column.show_header == "1"}
		  <th align='{$column.align}' colspan='{$column.span}'>{$column.header}</th>
		  {/if}
		
		{/foreach}
		
		{if $show_actions == '1'}<th>Actions</th>{/if}
		
	</tr>

    {****************************************************************************}
    {* Close the form used by the filter, pager and column sort.                *}
    {****************************************************************************}

	</form>

	<tr><td colspan='{$column_count}'><hr></td></tr>

    {* loop through the finding list and populate with values *}
	{foreach from=$rows item=row}
	
	<tr bgcolor='{cycle values="#EEEEEE,#CCCCCC"}'>
	
	    {if $show_checkboxes == '1'}
	
            {if $row.show_checkbox == '1'}<td><input type='checkbox'></td>{/if}
            
        {/if}
	
        {foreach from=$columns item=column}
        <td align='{$column.align}' colspan='{$column.span}'>{$row.row_data[$column.key]}</td>        
        {/foreach}
        
        
        {if $show_actions == '1'}
        <td>
            
            <table>
            <tr>
            {if $row.show_view   == '1'}
                <form action='{$view_target}' method='POST'>
                <input type='hidden' name='id' value='{$row.row_data[$row_index]}'>
                <td><input type='image' name='form_action' value='view' src='images/button_view.png'></td>
                </form>
            {/if}
                
            {if $row.show_update == '1'}
                <form action='{$update_target}' method='POST'>
                <input type='hidden' name='id' value='{$row.row_data[$row_index]}'>
                <td><input type='image' name='form_action' value='update' src='images/button_update.png'></td>
                </form>
            {/if}
                
            {if $row.show_delete == '1'}
                <form action='{$delete_target}' method='POST'>
                <input type='hidden' name='id' value='{$row.row_data[$row_index]}'>
                <td><input type='image' name='form_action' value='delete' src='images/button_delete.png'></td>
                </form>
            {/if}
            </tr>
            </table>
                                
        </td>                
        {/if}
        
	
	</tr>
	
	{/foreach}
	
	<tr><td colspan='{$column_count}'><hr></td></tr>
	
	{if $show_create == '1'} 
	
	<tr>
        <form action='{$create_target}' method='POST'>
        <td colspan='{$column_count}'><input type='image' name='form_action' value='create' src='images/button_create.png'></td>
        </form>
	
	</tr>
	   
	{/if}
	
</table>


{****************************************************************************}
{* Close the list's outer table row and close the table.                    *}
{****************************************************************************}

</td>
</tr>
</table>