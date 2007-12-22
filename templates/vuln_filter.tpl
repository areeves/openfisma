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

    <td>vuln type</td>
	<td>{html_options name='vuln_type_bool' values=$bool_values  output=$bool_options selected=$selected_type_bool }</td>
	<td>{html_options name='vuln_type'      values=$unique_types output=$unique_types selected=$selected_type }</td>
		
  </tr>


  <tr>

    <td>vuln desc primary</td>
	<td>{html_options name='vuln_desc_primary_bool' values=$bool_values  output=$bool_options selected=$selected_desc_primary_bool }</td>
	<td>{html_options name='vuln_desc_primary'      values=$unique_desc_primaries output=$unique_desc_primaries selected=$selected_desc_primary }</td>
		
  </tr>


  <tr>

    <td>vuln desc secondary</td>
	<td>{html_options name='vuln_desc_secondary_bool' values=$bool_values  output=$bool_options selected=$selected_desc_secondary_bool }</td>
	<td>{html_options name='vuln_desc_secondary'      values=$unique_desc_secondaries output=$unique_desc_secondaries selected=$selected_desc_secondary }</td>
		
  </tr>


  <tr><td colspan='3'><hr></td></tr>
  <tr>
	<td align='left' colspan='3'>
      <input type='hidden' name='form_target' value='vuln'>
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