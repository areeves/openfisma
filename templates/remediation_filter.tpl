{****************************************************************************}
{* We need to open up the form tag for in one of two instances:             *}
{*                                                                          *}
{* 1) if the filter is running as standalone (no pager)                     *}
{* 2) if the filter and pager are not running standalone                    *}
{*                                                                          *}
{* In the event of #2, it is up to the filter table to open up the form tag *}
{* that encompasses the filter, the pager, and the column headers that have *}
{* the sorting information                                                  *}
{****************************************************************************}

{if $filter_standalone == '1' || ($filter_standalone == '0' && $pager_standalone == '0')}
<form action='{$this_page}' method='POST'>
{/if}


{****************************************************************************}
{* The filter area has its own table (whereas the pager and list share a    *}
{* table for visual purposes.                                               *}
{****************************************************************************}

<table border='0'>

  <tr><th colspan='3' align='left'>Filters</th></tr>
  <tr><td colspan='3'><hr></td></tr>

  <tr>

    <td>finding source</td>
	<td>{html_options name='finding_source_bool' values=$bool_values  output=$bool_options selected=$selected_source_bool }</td>
	<td>{html_options name='finding_source'      options=$source_uniques                   selected=$selected_source }</td>
		
  <tr>

  <tr>

    <td>finding number</td>
	<td>{html_options name='finding_source_bool' values=$bool_values  output=$bool_options selected=$selected_source_bool }</td>
	<td>{html_options name='finding_source'      options=$finding_id_uniques               selected=$selected_source }</td>
		
  <tr>

  <tr>

    <td>POAM type</td>
	<td>{html_options name='poam_type_bool' values=$bool_values  output=$bool_options selected=$selected_type_bool }</td>
	<td>{html_options name='poam_type'      values=$unique_types output=$unique_types selected=$selected_type }</td>
		
  <tr>

  <tr>

	<td>POAM status</td>
	<td>{html_options name='poam_status_bool' values=$bool_values     output=$bool_options    selected=$selected_status_bool }</td>
	<td>{html_options name='poam_status'      values=$unique_statuses output=$unique_statuses selected=$selected_status }

	</td>

  </tr>

  <tr>

	<td>system</td>
	<td>{html_options name='poam_action_owner_bool' values=$bool_values     output=$bool_options selected=$selected_action_owner_bool }</td>
	<td>{html_options name='poam_action_owner'      options=$system_uniques                      selected=$selected_action_owner }

	</td>

  </tr>

  <tr><td colspan='3'><hr></td></tr>
  <tr>
	<td align='left' colspan='3'>
	  <input type='image' name='form_action' value='filter' src='images/button_filter.png'>
      <input type='image' name='form_action' value='reset'  src='images/button_reset.png'>
	</td>
  </tr>  

</table>


{****************************************************************************}
{* We need to close the form tag if the filter is standalone - this means   *}
{* that there is no filter, nor are there sort options on the column header *}
{* in the list.                                                             *}
{****************************************************************************}

{if $filter_standalone == '1'}
</form>
{/if}

<br>