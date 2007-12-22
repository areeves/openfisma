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

    <td>system name</td>
	<td>{html_options name='system_name_bool' values=$bool_values  output=$bool_options selected=$selected_name_bool }</td>
	<td>{html_options name='system_name'      values=$unique_names output=$unique_names selected=$selected_name }</td>
		
  </tr>


  <tr>

    <td>system type</td>
	<td>{html_options name='system_type_bool' values=$bool_values  output=$bool_options selected=$selected_type_bool }</td>
	<td>{html_options name='system_type'      values=$unique_types output=$unique_types selected=$selected_type }</td>
		
  </tr>


  <tr>

    <td>system confidentiality</td>
	<td>{html_options name='system_confidentiality_bool' values=$bool_values  output=$bool_options selected=$selected_confidentiality_bool }</td>
	<td>{html_options name='system_confidentiality'      values=$unique_confidentialities output=$unique_confidentialities selected=$selected_confidentiality }</td>
		
  </tr>


  <tr>

    <td>system integrity</td>
	<td>{html_options name='system_integrity_bool' values=$bool_values  output=$bool_options selected=$selected_integrity_bool }</td>
	<td>{html_options name='system_integrity'      values=$unique_integrities output=$unique_integrities selected=$selected_integrity }</td>
		
  </tr>


  <tr>

    <td>system availability</td>
	<td>{html_options name='system_availability_bool' values=$bool_values  output=$bool_options selected=$selected_availability_bool }</td>
	<td>{html_options name='system_availability'      values=$unique_availabilities output=$unique_availabilities selected=$selected_availability }</td>
		
  </tr>


  <tr><td colspan='3'><hr></td></tr>
  <tr>
	<td align='left' colspan='3'>
      <input type='hidden' name='form_target' value='system'>
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