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

    <td>prod name</td>
	<td>{html_options name='prod_name_bool' values=$bool_values  output=$bool_options selected=$prod_name_bool }</td>
	<td>{html_options name='prod_name'      values=$prod_names output=$prod_names selected=$prod_name }</td>
		
  </tr>


  <tr>

    <td>prod version</td>
	<td>{html_options name='prod_version_bool' values=$bool_values  output=$bool_options selected=$prod_version_bool }</td>
	<td>{html_options name='prod_version'      values=$prod_versions output=$prod_versions selected=$prod_version }</td>
		
  </tr>


  <tr>

    <td>prod desc</td>
	<td>{html_options name='prod_desc_bool' values=$bool_values  output=$bool_options selected=$prod_desc_bool }</td>
	<td>{html_options name='prod_desc'      values=$prod_descs output=$prod_descs selected=$prod_desc }</td>
		
  </tr>


  <tr><td colspan='3'><hr></td></tr>
  <tr>
	<td align='left' colspan='3'>
      <input type='hidden' name='form_target' value='product'>
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