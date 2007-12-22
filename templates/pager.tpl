{****************************************************************************}
{* For visual reasons, both the pager and the resulting list tables are to  *}
{* to be contained in separate, single-columned rows in an outer table.     *}
{****************************************************************************}

<table border='0'>

<tr><th align='left' colspan='11'>List</th></tr>
<tr><td><hr></td></tr>

<tr>
<td align='right'>


{****************************************************************************}
{* If the pager is running in standalone mode, then we assume that the form *}
{* tag has not been opened yet and open it here.                            *}
{****************************************************************************}

{if $pager_form_start == '1'}
<form actiom='{$this_page}' method='POST'>
{/if}

{* pager state values *}
<input type='hidden' name='current_page' value='{$current_page}'>
<input type='hidden' name='page_size'    value='{$page_size}'>

<table border='0'>

  <tr>

	<td><input type='image' name='form_action' value='page_jump'  src='images/button_jump.png'></td>
	<td>{html_options       name='page_jump'   values=$page_jumps output=$page_jumps selected=$current_page}</td>
	<td> | </td>
	<td><input type='image' name='form_action' value='page_first' src='images/button_first.png'></td>
	<td><input type='image' name='form_action' value='page_prev'  src='images/button_prev.png'></td>
	
	<td> | page {$current_page} / {$last_page} | </td>
	
	<td><input type='image' name='form_action' value='page_next'  src='images/button_next.png'></td>
	<td><input type='image' name='form_action' value='page_last'  src='images/button_last.png'></td>
	<td> | </td>
	<td><input type='image' name='form_action' value='page_size'  src='images/button_size.png'></td>
	<td>{html_options       name='page_size'   values=$page_sizes output=$page_sizes selected=$page_size}</td>

  <tr>

  <tr><td align='right' colspan='11'> listing {$list_start} - {$list_end} of {$list_size} </td></tr>

</table>


{****************************************************************************}
{* If the pager is running in standalone mode, then we assume that the form *}
{* tag will not be closed by a column sort later in the page and close it   *}
{* here.                                                                    *}
{****************************************************************************}

{if $pager_form_close == '1'}
</form>
{/if}


{****************************************************************************}
{* Close the pager's outer table row.                                       *}
{****************************************************************************}

</td>
</tr>